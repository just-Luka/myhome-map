<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\StreamController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ListingController::class, 'index']);
Route::middleware('throttle:60,1')->get('/api/stream', [StreamController::class, 'stream']);
