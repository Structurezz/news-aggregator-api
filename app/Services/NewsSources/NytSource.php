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

        return collect($docs)
            ->map(fn($doc) => ArticleDto::fromNyt($doc))
            ->take($limit)
            ->all();
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
