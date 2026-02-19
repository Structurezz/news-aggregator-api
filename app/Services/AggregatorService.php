<?php

namespace App\Services;

use App\Contracts\NewsFetcher;
use App\DTOs\ArticleDto;

class AggregatorService
{
    protected array $sources;

    public function __construct(array $sources = [])
    {
        $this->sources = array_filter($sources, fn($s) => $s instanceof NewsFetcher);
    }

  
    public function aggregate(int $perSource = 50): array
    {
        $allArticles = [];

        foreach ($this->sources as $source) {
            $sourceName = class_basename($source);

            try {
                $articles = $source->fetchRecent($perSource);
                if (!is_array($articles)) {
                    $articles = [];
                }

                foreach ($articles as $article) {
                    if ($article instanceof ArticleDto) {
                        $allArticles[$article->url] = $article; 
                    }
                }
            } catch (\Throwable $e) {
              
                \Log::error("Aggregator failed on {$sourceName}: " . $e->getMessage(), [
                    'exception' => $e
                ]);
            }
        }

        return array_values($allArticles); 
    }
}
