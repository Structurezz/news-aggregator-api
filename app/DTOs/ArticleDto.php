<?php

namespace App\DTOs;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class ArticleDto extends Data
{
    public function __construct(
        public string $title,
        public ?string $description,
        public ?string $content,
        public string $url,
        public ?string $image_url,
        public Carbon $published_at, // Use Carbon instance
        public string $source_name,
        public ?string $author = null,
        public ?string $category = 'general',
    ) {
        //
    }

    /**
     * Convert a single Guardian API item into ArticleDto
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['fields']['headline'] ?? 'No title',
            description: $data['fields']['trailText'] ?? null,
            content: $data['fields']['bodyText'] ?? null,
            url: $data['webUrl'],
            image_url: $data['fields']['thumbnail'] ?? null,
            published_at: isset($data['webPublicationDate'])
                ? Carbon::parse($data['webPublicationDate'])
                : now(),
            source_name: 'The Guardian',
            author: $data['fields']['byline'] ?? 'Editorial',
            category: $data['sectionName'] ?? 'general',
        );
    }

    /**
     * Helper to map a collection of API items to DTOs
     */
    public static function fromCollection(array $items): array
    {
        return collect($items)->map(fn($item) => self::fromArray($item))->all();
    }
}
