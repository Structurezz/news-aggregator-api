<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\FetchNewsJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

dispatch(new FetchNewsJob());


Schedule::job(new FetchNewsJob)
    ->daily()
    ->withoutOverlapping()
    ->onOneServer();


Schedule::command('model:prune --model=App\\Models\\Article --days=30')
    ->daily();
