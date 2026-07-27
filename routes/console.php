<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cron quotidien de relance automatique des loyers (7j, 5j, 3j, 1j avant échéance)
Schedule::command('app:send-rent-reminders')->dailyAt('08:00');
