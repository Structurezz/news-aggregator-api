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
            // Load all tagged news sources
            $sources = iterator_to_array(app()->tagged('news-fetchers'));

            if (empty($sources)) {
                Log::warning('No news-fetchers tagged – check NewsSourceServiceProvider.');
                return;
            }

            $aggregator = new AggregatorService($sources);

            // Aggregate articles (limit per source)
            $articles = $aggregator->aggregate(50);
            $totalArticles = count($articles);
            Log::info("Aggregator returned {$totalArticles} unique articles.");

            if ($totalArticles === 0) {
                Log::info('No new articles to store.');
                return;
            }

            // Safely store articles
            $savedCount = 0;
            foreach ($articles as $dto) {
                try {
                    $savedCount += $articleService->storeMany([$dto]);
                } catch (\Throwable $e) {
                    Log::warning("Failed to store article '{$dto->title}': " . $e->getMessage());
                }
            }

            Log::info("FetchNewsJob completed. {$savedCount}/{$totalArticles} new articles saved.");

        } catch (\Throwable $e) {
            Log::error('FetchNewsJob failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            throw $e; // allow retry
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('FetchNewsJob permanently failed after retries: ' . $exception->getMessage(), [
            'exception' => $exception
        ]);
    }
}
