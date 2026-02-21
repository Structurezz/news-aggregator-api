<?php

namespace App\Jobs;

use App\Services\AggregatorService;
use App\Services\ArticleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    public function handle(ArticleService $articleService): void
    {
        Log::info('FetchNewsJob started – aggregating from all sources.');

        try {
            $sources = iterator_to_array(app()->tagged('news-fetchers'));

            if (empty($sources)) {
                Log::warning('No news-fetchers tagged.');
                return;
            }

            $aggregator = new AggregatorService($sources);

           
            $articles = $aggregator->aggregate(50);
            
            if (empty($articles)) {
                Log::info('No articles retrieved from sources.');
                return;
            }

     
            $affectedRows = $articleService->storeMany($articles);

            Log::info("FetchNewsJob completed. Affected rows: {$affectedRows}");

        } catch (\Throwable $e) {
            Log::error('FetchNewsJob failed: ' . $e->getMessage(), ['exception' => $e]);
            throw $e; 
        }
    }
}