<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\ScrapeController;
use App\Http\Controllers\StreamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ── Site password gate ────────────────────────────────────────────────────────
Route::get('/login', fn () => view('login'))->name('login');

Route::post('/login', function (Request $request) {
    if ($request->input('password') === 'myhomemap2024') {
        session(['site_auth' => true]);
        return redirect('/');
    }
    return back()->with('error', 'Wrong password.');
})->name('login.post');

Route::post('/logout', function () {
    session()->forget('site_auth');
    return redirect()->route('login');
})->name('logout');

Route::middleware(function ($request, $next) {
    if (! session('site_auth')) return redirect()->route('login');
    return $next($request);
})->group(function () {
    Route::get('/', [ListingController::class, 'index']);
    Route::middleware('throttle:60,1')->get('/api/stream', [StreamController::class, 'stream']);
});

Route::get('/scrape', [ScrapeController::class, 'page'])->name('scrape.page');
Route::post('/scrape/auth', [ScrapeController::class, 'auth'])->name('scrape.auth');
Route::get('/scrape/run', [ScrapeController::class, 'run'])->name('scrape.run');
