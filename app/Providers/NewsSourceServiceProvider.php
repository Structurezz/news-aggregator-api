<?php

namespace App\Providers;

use App\Contracts\NewsFetcher;
use App\Services\NewsSources\GuardianSource;
use App\Services\NewsSources\NewsApiSource;
use App\Services\NewsSources\NytSource;
use Illuminate\Support\ServiceProvider;

class NewsSourceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
       
        $this->app->bind(NewsApiSource::class, fn($app) => new NewsApiSource());
        $this->app->bind(GuardianSource::class, fn($app) => new GuardianSource());
        $this->app->bind(NytSource::class, fn($app) => new NytSource());

    
        $this->app->tag([
            NewsApiSource::class,
            GuardianSource::class,
            NytSource::class,
        ], 'news-fetchers');
    }

    public function boot(): void
    {
        //
    }
}
