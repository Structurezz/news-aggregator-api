<?php

use App\Http\Controllers\Api\ArticleController;
use App\Models\Article;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

Route::prefix('v1')->group(function () {

    Route::get('articles', [ArticleController::class, 'index'])
        ->name('api.v1.articles.index');

    Route::get('articles/{article}', [ArticleController::class, 'show'])
        ->name('api.v1.articles.show');

    Route::get('status', function () {

      
        $lastUpdatedRaw = Article::max('updated_at');

       
        $lastUpdated = $lastUpdatedRaw
            ? Carbon::parse($lastUpdatedRaw)->toIso8601String()
            : null;

        return response()->json([
            'status' => 'operational',
            'articles_total' => Article::count(),
            'last_updated' => $lastUpdated,
            'sources' => ['The Guardian', 'NewsAPI.org', 'New York Times'],
            'last_fetch' => now()->toIso8601String(),
            'laravel_version' => app()->version(),
        ]);
    })->name('api.v1.status');

});
