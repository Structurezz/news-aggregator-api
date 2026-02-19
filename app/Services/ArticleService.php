<?php

namespace App\Services;

use App\DTOs\ArticleDto;
use App\Models\Article;

class ArticleService
{
    public function storeMany(array $dtos): int
    {
        $count = 0;

        foreach ($dtos as $dto) {
            /** @var ArticleDto $dto */

            // Generate a SHA-256 hash of the URL for deduplication
            $urlHash = hash('sha256', $dto->url);

            // Skip if this URL hash already exists
            if (Article::where('url_hash', $urlHash)->exists()) {
                continue;
            }

            // Save the article
            Article::create([
                'title'        => $dto->title,
                'description'  => $dto->description,
                'content'      => $dto->content,
                'url'          => $dto->url,
                'url_hash'     => $urlHash,
                'image_url'    => $dto->image_url,
                'published_at' => $dto->published_at,
                'source_name'  => $dto->source_name,
                'author'       => $dto->author,
                'category'     => $dto->category,
            ]);

            $count++;
        }

        return $count;
    }
}
