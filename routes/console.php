<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('family:send-daily-celebrations')
    ->dailyAt((string) config('family_notifications.run_at', '08:00'))
    ->timezone((string) config('app.timezone', 'UTC'))
    ->withoutOverlapping();
