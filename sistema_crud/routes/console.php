<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
//revisar datos  de laravel
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// revisa datos
Schedule::command('citas:cancelar-expiradas')->everyMinute();