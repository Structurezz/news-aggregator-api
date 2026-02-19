<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsFetcher;
use App\DTOs\ArticleDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class NewsApiSource implements NewsFetcher
{
    public function fetchRecent(int $limit = 30, ?string $keyword = null): array
    {
        $query = [
            'apiKey'   => config('services.newsapi.key'),
            'pageSize' => min($limit, 100),
            'language' => 'en',
            'sortBy'   => 'publishedAt',
            'q'        => $keyword ?: 'news OR world OR breaking OR technology OR sport',
            'from'     => now()->subDay()->format('Y-m-d'),     // last 24 hours
        ];
    
        $response = Http::get('https://newsapi.org/v2/everything', $query);
    
        if ($response->failed()) {
            \Log::warning('NewsAPI failed', $response->json());
            return [];
        }
    
        $articles = $response->json('articles') ?? [];
    
        return collect($articles)->map(fn($item) => new ArticleDto(
            title: $item['title'] ?? 'No title',
            description: $item['description'] ?? null,
            content: $item['content'] ?? null,
            url: $item['url'],
            image_url: $item['urlToImage'] ?? null,
            published_at: Carbon::parse($item['publishedAt'] ?? now()),
            source_name: $item['source']['name'] ?? 'NewsAPI',
            author: $item['author'] ?? null,
            category: 'general',
        ))->all();
    }
}