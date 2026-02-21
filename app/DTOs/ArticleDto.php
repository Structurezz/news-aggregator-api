<?php

namespace App\DTOs;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class ArticleDto extends Data
{
    public function __construct(
        public string $title,
        public string $url,
        public Carbon $published_at,
        public string $source_name,
        public ?string $description = null,
        public ?string $content = null,
        public ?string $image_url = null,
        public ?string $author = null,
        public ?string $category = null, 
    ) {}

  
    public static function fromGuardian(array $data): self
    {
        return new self(
            title: $data['fields']['headline'] ?? 'No title',
            url: $data['webUrl'],
            published_at: isset($data['webPublicationDate'])
                ? Carbon::parse($data['webPublicationDate'])
                : now(),
            source_name: 'The Guardian',
            description: $data['fields']['trailText'] ?? null,
            content: $data['fields']['bodyText'] ?? null,
            image_url: $data['fields']['thumbnail'] ?? null,
            author: $data['fields']['byline'] ?? 'Editorial',
            category: $data['sectionName'] ?? 'general',
        );
    }


    public static function fromNewsApi(array $item): self
    {
        return new self(
            title: $item['title'] ?? 'No title',
            url: isset($item['url']) ? substr($item['url'], 0, 1024) : 'No URL',
            published_at: isset($item['publishedAt']) ? Carbon::parse($item['publishedAt']) : now(),
            source_name: $item['source']['name'] ?? 'NewsAPI',
            description: $item['description'] ?? null,
            content: $item['content'] ?? null,
            image_url: isset($item['urlToImage']) ? substr($item['urlToImage'], 0, 1024) : null,
            author: $item['author'] ?? null,
            category: 'general',
        );
    }

  
    public static function fromNyt(array $doc): self
    {
        $multimedia = collect($doc['multimedia'] ?? [])->firstWhere('subtype', 'thumbnail');

        return new self(
            title: $doc['headline']['main'] ?? 'No title',
            url: isset($doc['web_url']) ? substr($doc['web_url'], 0, 1024) : 'No URL',
            published_at: isset($doc['pub_date']) ? Carbon::parse($doc['pub_date']) : now(),
            source_name: 'The New York Times',
            description: $doc['abstract'] ?? $doc['lead_paragraph'] ?? null,
            content: $doc['lead_paragraph'] ?? null,
            image_url: $multimedia ? substr('https://www.nytimes.com/' . $multimedia['url'], 0, 1024) : null,
            author: $doc['byline']['original'] ?? null,
            category: $doc['section_name'] ?? 'general',
        );
    }
}
