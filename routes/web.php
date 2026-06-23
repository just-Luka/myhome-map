<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
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
    Route::get('/api/my-saves',     [SavedListingController::class, 'mySaves'])->name('save.mine');
    Route::get('/api/team-saves',   [SavedListingController::class, 'teamSaves'])->name('save.team');
});

// ── CEO dashboard ─────────────────────────────────────────────────────────────
Route::middleware(\App\Http\Middleware\CeoAccess::class)->group(function () {
    Route::get('/dashboard',          [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/dashboard/settings',[DashboardController::class, 'updateSettings'])->name('dashboard.settings');
    Route::post('/dashboard/invite',  [DashboardController::class, 'generateInvite'])->name('dashboard.invite');
    Route::get('/dashboard/export',    [DashboardController::class, 'export'])->name('dashboard.export');
    Route::post('/dashboard/logo',     [DashboardController::class, 'uploadLogo'])->name('dashboard.logo');
    Route::delete('/dashboard/logo',   [DashboardController::class, 'removeLogo'])->name('dashboard.logo.remove');
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

// ── Dev helpers (remove before production) ───────────────────────────────────
Route::get('/dev/splash', function () {
    $user = auth()->user();
    $org  = $user->organization;
    session()->flash('welcome_splash', [
        'name' => $user->name,
        'org'  => $org?->name ?? 'Demo Agency',
        'logo' => $org?->logo,
    ]);
    return redirect('/');
})->middleware('auth');

// ── Scrape (internal) ─────────────────────────────────────────────────────────
Route::get('/scrape',       [ScrapeController::class, 'page'])->name('scrape.page');
Route::post('/scrape/auth', [ScrapeController::class, 'auth'])->name('scrape.auth');
Route::get('/scrape/run',   [ScrapeController::class, 'run'])->name('scrape.run');
