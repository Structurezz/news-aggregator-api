<?php

namespace App\Services;

use App\DTOs\ArticleDto;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleService
{
    public function exists(string $url): bool
    {
        return Article::where('url_hash', hash('sha256', $url))->exists();
    }

    public function storeMany(array $dtos): int
    {
        if (empty($dtos)) {
            return 0;
        }

        $data = [];
        $now = now();

        foreach ($dtos as $dto) {
         

            $url = trim($dto->url ?? '');
            if (empty($url)) {
                continue;
            }

            $urlHash = hash('sha256', $url);

            $data[] = [
                'title'        => Str::limit($dto->title ?? '', 1024, '...'),
                'description'  => $dto->description,
                'content'      => $dto->content,
                'url'          => Str::limit($url, 2048),
                'url_hash'     => $urlHash,
                'image_url'    => Str::limit($dto->image_url ?? '', 2048),
                'published_at' => $dto->published_at,
                'source_name'  => Str::limit($dto->source_name ?? 'Unknown', 120),
                'author'       => Str::limit($dto->author ?? '', 255),
                'category'     => Str::limit($dto->category ?? '', 100),
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        if (empty($data)) {
            return 0;
        }

        return DB::transaction(function () use ($data) {
            $affected = 0;
            $chunks = array_chunk($data, 500);

            foreach ($chunks as $chunk) {
                $affected += DB::table('articles')->upsert(
                    $chunk,
                    ['url'],
                    [
                        'title', 'description', 'content', 'image_url', 'published_at',
                        'source_name', 'author', 'category', 'updated_at',
                    ]
                );
            }

            return $affected;
        });
    }
}