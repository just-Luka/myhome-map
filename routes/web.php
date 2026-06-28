<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\SavedListingController;
use App\Http\Controllers\ScrapeController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Invite (public — no auth needed, link is the credential) ──────────────────
Route::get('/invite/{token}',  [InviteController::class, 'show'])->name('invite.show');
Route::post('/invite/{token}', [InviteController::class, 'accept'])->name('invite.accept');

// ── Public map ────────────────────────────────────────────────────────────────
Route::get('/', [ListingController::class, 'index']);
Route::middleware(['auth', 'throttle:60,1'])->get('/api/stream', [StreamController::class, 'stream']);

// ── Authenticated users (saved listings + team info) ──────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/api/save',        [SavedListingController::class, 'toggle'])->name('save.toggle');
    Route::post('/api/save/custom', [SavedListingController::class, 'storeCustom'])->name('save.custom');
    Route::patch('/api/save/note',   [SavedListingController::class, 'updateNote'])->name('save.note');
    Route::patch('/api/save/links',  [SavedListingController::class, 'updateLinks'])->name('save.links');
    Route::patch('/api/save/update', [SavedListingController::class, 'updateEntry'])->name('save.update');
    Route::get('/api/my-saves',     [SavedListingController::class, 'mySaves'])->name('save.mine');
    Route::get('/api/all-saves',    [SavedListingController::class, 'allSaves'])->name('save.all');
    Route::get('/api/team-saves',   [SavedListingController::class, 'teamSaves'])->name('save.team');
});

// ── Owner (CEO) ───────────────────────────────────────────────────────────────
Route::middleware(\App\Http\Middleware\CeoAccess::class)->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard',  [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/employees',                    [EmployeeController::class, 'index'])->name('employees');
    Route::get('/employees/{user}',             [EmployeeController::class, 'show'])->name('employees.show');
    Route::delete('/employees/{user}',          [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::patch('/employees/{user}/limit',     [EmployeeController::class, 'updateLimit'])->name('employees.limit');
    Route::get('/employees/{user}/export',      [EmployeeController::class, 'export'])->name('employees.export');
    Route::delete('/saves/{save}',              [EmployeeController::class, 'destroySave'])->name('saves.destroy');
    Route::get('/settings',   [DashboardController::class, 'settings'])->name('settings');
    Route::patch('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
    Route::post('/invite',    [DashboardController::class, 'generateInvite'])->name('invite');
    Route::get('/export',     [DashboardController::class, 'export'])->name('export');
    Route::post('/logo',      [DashboardController::class, 'uploadLogo'])->name('logo');
    Route::delete('/logo',    [DashboardController::class, 'removeLogo'])->name('logo.remove');
});

// ── Super admin ───────────────────────────────────────────────────────────────
Route::middleware(\App\Http\Middleware\SuperAdmin::class)->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                             [SuperAdminController::class, 'index'])->name('index');

    // Organizations
    Route::get('/organizations',                [SuperAdminController::class, 'orgs'])->name('orgs');
    Route::post('/organizations',               [SuperAdminController::class, 'createOrg'])->name('org.create');
    Route::get('/organizations/{org}',          [SuperAdminController::class, 'orgShow'])->name('orgs.show');
    Route::patch('/organizations/{org}',        [SuperAdminController::class, 'updateOrg'])->name('orgs.update');
    Route::delete('/organizations/{org}',       [SuperAdminController::class, 'deleteOrg'])->name('orgs.delete');
    Route::post('/organizations/{org}/invite',  [SuperAdminController::class, 'generateCeoInvite'])->name('org.invite');

    // Users
    Route::get('/users',                        [SuperAdminController::class, 'users'])->name('users');
    Route::get('/users/{user}/edit',            [SuperAdminController::class, 'editUser'])->name('users.edit');
    Route::patch('/users/{user}',               [SuperAdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}',              [SuperAdminController::class, 'deleteUser'])->name('users.delete');

    // Activity
    Route::get('/activity',                     [SuperAdminController::class, 'activity'])->name('activity');
    Route::get('/activity/export',              [SuperAdminController::class, 'exportActivity'])->name('activity.export');
});

// ── Scrape (super admin only) ─────────────────────────────────────────────────
Route::middleware(\App\Http\Middleware\SuperAdmin::class)->group(function () {
    Route::get('/scrape',     [ScrapeController::class, 'page'])->name('scrape.page');
    Route::get('/scrape/run', [ScrapeController::class, 'run'])->name('scrape.run');
});
