<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule document expiry reminders to run daily at midnight UTC
Schedule::command('documents:process-expiry-reminders')
    ->dailyAt('00:00')
    ->timezone('UTC');

// Schedule share cleanup to run weekly on Sundays at 2 AM UTC
Schedule::command('shares:cleanup-expired')
    ->weeklyOn(0, '02:00')
    ->timezone('UTC');
