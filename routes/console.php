<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Refresh listings every 3 hours — keeps data within 5-day window fresh
Schedule::command('scrape:myhome --pages=30')->everyThreeHours();
