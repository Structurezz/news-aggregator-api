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
                Log::warning('No news-fetchers tagged – check NewsSourceServiceProvider.', []);
                return;
            }

            $aggregator = new AggregatorService($sources);

      
            $articles = $aggregator->aggregate(50);
            $totalArticles = count($articles);

            Log::info("Aggregator returned {$totalArticles} articles.");

            if ($totalArticles === 0) {
                Log::info('No new articles to store.');
                return;
            }

            $savedCount = 0;

            foreach ($articles as $dto) {
                try {
                  
                    if (!$articleService->exists($dto->url)) {
                        $savedCount += $articleService->storeMany([$dto]);
                    }
                } catch (\Throwable $e) {
                    Log::warning(
                        "Failed to store article '{$dto->title}' ({$dto->url})",
                        ['exception' => $e]
                    );
                }
            }

            Log::info("FetchNewsJob completed. {$savedCount}/{$totalArticles} new articles saved.");

        } catch (\Throwable $e) {
            Log::error('FetchNewsJob failed: ' . $e->getMessage(), ['exception' => $e]);
            throw $e; 
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('FetchNewsJob permanently failed after retries: ' . $exception->getMessage(), [
            'exception' => $exception
        ]);
    }
}
