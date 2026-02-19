<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsFetcher;
use App\DTOs\ArticleDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class NytSource implements NewsFetcher
{
    public function fetchRecent(int $limit = 30, ?string $keyword = null): array
    {
        $params = [
            'api-key'    => config('services.nytimes.key'),
            'page'       => 0,
            'sort'       => 'newest',
            'begin_date' => now()->subDay()->format('Ymd'),     // last 24 hours
            'end_date'   => now()->format('Ymd'),
            'fl'         => 'headline,abstract,lead_paragraph,web_url,multimedia,byline,pub_date,section_name',
        ];
        
        if ($keyword) {
            $params['q'] = $keyword;
        } else {
            $params['q'] = 'news OR world OR breaking OR technology OR sport';
        }

        $response = Http::get(config('services.nytimes.base_url') . '/articlesearch/v2.json', $params);

        if ($response->failed()) {
            \Log::warning('NYT API request failed', $response->json());
            return [];
        }

        $docs = $response->json('response.docs') ?? [];

        return collect($docs)->map(function ($doc) {
            $multimedia = collect($doc['multimedia'] ?? [])->firstWhere('subtype', 'thumbnail');

            return new ArticleDto(
                title: $doc['headline']['main'] ?? 'No title',
                description: $doc['abstract'] ?? $doc['lead_paragraph'] ?? null,
                content: $doc['lead_paragraph'] ?? null,
                url: $doc['web_url'],
                image_url: $multimedia ? 'https://www.nytimes.com/' . $multimedia['url'] : null,
                published_at: isset($doc['pub_date'])
                    ? Carbon::parse($doc['pub_date'])
                    : now(),
                source_name: 'The New York Times',
                author: $doc['byline']['original'] ?? null,
                category: $doc['section_name'] ?? 'general',
            );
        })->take($limit)->all();
    }
}