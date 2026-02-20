<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsFetcher;
use App\DTOs\ArticleDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
            'from'     => now()->subDay()->format('Y-m-d'),
        ];

        $response = Http::get('https://newsapi.org/v2/everything', $query);

        if ($response->failed()) {
            \Log::warning('NewsAPI request failed', ['response' => $response->json()]);
            return [];
        }

        $articles = $response->json('articles') ?? [];

        return collect($articles)->map(fn($item) => new ArticleDto(
            title: $item['title'] ?? 'No title',
            description: $item['description'] ?? null,
            content: $item['content'] ?? null,
            url: Str::limit($item['url'], 1024),
            image_url: isset($item['urlToImage']) ? Str::limit($item['urlToImage'], 1024) : null,
            published_at: isset($item['publishedAt']) ? Carbon::parse($item['publishedAt']) : now(),
            source_name: $item['source']['name'] ?? 'NewsAPI',
            author: $item['author'] ?? null,
            category: 'general',
        ))->take($limit)->all();
    }
}
