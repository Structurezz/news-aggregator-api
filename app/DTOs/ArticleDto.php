<?php

namespace App\DTOs;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class ArticleDto extends Data
{
    public function __construct(
        public string $title,
        public string $url,
        public \Carbon\Carbon $published_at,
        public string $source_name,
        public ?string $description = null,
        public ?string $content = null,
        public ?string $image_url = null,
        public ?string $author = null,
        public ?string $category = null, 
    ) {}

    public static function fromArray(array $data): self
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
}