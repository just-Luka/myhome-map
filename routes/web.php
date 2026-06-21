<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\ScrapeController;
use App\Http\Controllers\StreamController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ListingController::class, 'index']);
Route::middleware('throttle:60,1')->get('/api/stream', [StreamController::class, 'stream']);

Route::get('/scrape', [ScrapeController::class, 'page'])->name('scrape.page');
Route::post('/scrape/auth', [ScrapeController::class, 'auth'])->name('scrape.auth');
Route::get('/scrape/run', [ScrapeController::class, 'run'])->name('scrape.run');
