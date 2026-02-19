<?php

namespace App\Console\Commands;

use App\Contracts\NewsFetcher;
use App\Services\ArticleService;
use Illuminate\Console\Command;

class FetchArticlesCommand extends Command
{
    protected $signature = 'news:fetch {--limit=30} {--keyword=}';

    protected $description = 'Fetch recent articles from all configured news sources';

    public function handle(ArticleService $articleService)
    {
        $limit = (int) $this->option('limit');
        $keyword = $this->option('keyword') ?: null;

        $sources = app()->tagged('news-fetchers');

        $this->info('Starting news fetch...');
        $totalNew = 0;

        foreach ($sources as $source) {
            /** @var NewsFetcher $source */
            $sourceName = class_basename($source);

            $this->info(" → Fetching from {$sourceName} ...");

            try {
                $articles = $source->fetchRecent($limit, $keyword);
                $saved = $articleService->storeMany($articles);

                $this->info("   Saved {$saved} new articles");
                $totalNew += $saved;
            } catch (\Throwable $e) {
                $this->error("   Failed: " . $e->getMessage());
                \Log::error("News fetch failed for {$sourceName}", [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $this->info("Fetch completed. Total new articles: {$totalNew}");
    }
}