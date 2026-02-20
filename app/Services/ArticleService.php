<?php

namespace App\Services;

use App\DTOs\ArticleDto;
use App\Models\Article;

class ArticleService
{
   
    public function exists(string $url): bool
    {
        $urlHash = hash('sha256', $url);
        return Article::where('url_hash', $urlHash)->exists();
    }

   
    public function storeMany(array $dtos): int
    {
        $count = 0;

        foreach ($dtos as $dto) {
            /** @var ArticleDto $dto */

            // Skip if article already exists
            if ($this->exists($dto->url)) {
                continue;
            }

            // Ensure string lengths are safe for DB columns
            $title       = mb_substr($dto->title ?? '', 0, 255);
            $url         = mb_substr($dto->url ?? '', 0, 255);
            $urlHash     = hash('sha256', $dto->url ?? '');
            $imageUrl    = mb_substr($dto->image_url ?? '', 0, 1024);
            $author      = mb_substr($dto->author ?? '', 0, 255);
            $sourceName  = mb_substr($dto->source_name ?? '', 0, 255);
            $category    = mb_substr($dto->category ?? '', 0, 255);

            // Save the article
            Article::create([
                'title'        => $title,
                'description'  => $dto->description,
                'content'      => $dto->content,
                'url'          => $url,
                'url_hash'     => $urlHash,
                'image_url'    => $imageUrl,
                'published_at' => $dto->published_at,
                'source_name'  => $sourceName,
                'author'       => $author,
                'category'     => $category,
            ]);

            $count++;
        }

        return $count;
    }
}
