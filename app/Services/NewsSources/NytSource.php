<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsFetcher;
use App\DTOs\ArticleDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NytSource implements NewsFetcher
{
    public function fetchRecent(int $limit = 30, ?string $keyword = null): array
    {
        $baseUrl = rtrim(config('services.nytimes.base_url', 'https://api.nytimes.com/svc'), '/');
        $url = rtrim(config('services.nytimes.base_url'), '/') . '/search/v2/articlesearch.json';


        $params = [
            'api-key'    => config('services.nytimes.key'),
            'page'       => 0,
            'sort'       => 'newest',
            'begin_date' => now()->subDay()->format('Ymd'),
            'end_date'   => now()->format('Ymd'),
            'fl'         => 'headline,abstract,lead_paragraph,web_url,multimedia,byline,pub_date,section_name',
            'q'          => $keyword ?: 'news OR world OR breaking OR technology OR sport',
        ];

        $response = Http::get($url, $params);

        if ($response->failed()) {
            $this->safeLogWarning('NYT API request failed', $response);
            return [];
        }

        $docs = $response->json('response.docs') ?? [];

        return collect($docs)->map(function ($doc) {
            $multimedia = collect($doc['multimedia'] ?? [])->firstWhere('subtype', 'thumbnail');

            return new ArticleDto(
                title: $doc['headline']['main'] ?? 'No title',
                description: $doc['abstract'] ?? $doc['lead_paragraph'] ?? null,
                content: $doc['lead_paragraph'] ?? null,
                url: isset($doc['web_url']) ? Str::limit($doc['web_url'], 1024) : null,
                image_url: $multimedia ? Str::limit('https://www.nytimes.com/' . $multimedia['url'], 1024) : null,
                published_at: isset($doc['pub_date']) ? Carbon::parse($doc['pub_date']) : now(),
                source_name: 'The New York Times',
                author: $doc['byline']['original'] ?? null,
                category: $doc['section_name'] ?? 'general',
            );
        })->take($limit)->all();
    }

    protected function safeLogWarning(string $message, $response): void
    {
     
        $context = [
            'status'   => $response && method_exists($response, 'status') ? $response->status() : null,
            'response' => $response && method_exists($response, 'json') ? $response->json() ?? [] : [],
            'body'     => $response && method_exists($response, 'body') ? $response->body() : null,
        ];
    
        \Log::warning($message, $context);
    }
    
    
}
