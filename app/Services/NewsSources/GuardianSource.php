<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsFetcher;
use App\DTOs\ArticleDto;
use Illuminate\Support\Facades\Http;

class GuardianSource implements NewsFetcher
{
    public function fetchRecent(int $limit = 30, ?string $keyword = null): array
    {
        $query = [
            'api-key'     => config('services.guardian.key'),
            'page-size'   => min($limit, 50),
            'show-fields' => 'headline,trailText,bodyText,thumbnail,byline',
            'order-by'    => 'newest',
            'from-date'   => now()->subDay()->format('Y-m-d'),
            'to-date'     => now()->format('Y-m-d'),
        ];

        $query['q'] = $keyword ?: 'news OR world OR breaking OR technology OR sport OR politics';

        $response = Http::get(config('services.guardian.base_url') . '/search', $query);

        if ($response->failed()) {
            \Log::warning('Guardian API failed', $response->json());
            return [];
        }

        $results = $response->json('response.results') ?? [];

      
        return ArticleDto::fromCollection($results);
    }
}
