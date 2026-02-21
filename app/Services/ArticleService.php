<?php

namespace App\Services;

use App\DTOs\ArticleDto;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArticleService
{
 
    public function exists(string $url): bool
    {
        if (empty(trim($url))) {
            return false;
        }

        return Article::where('url', $url)->exists();
    }

 
    public function storeMany(array $dtos): int
    {
        if (empty($dtos)) return 0;
    
        $data = array_map(fn($dto) => [
            'title' => $dto->title,
            'url' => $dto->url,
            'published_at' => $dto->published_at,
            'source_name' => $dto->source_name,
            'description' => $dto->description,
            'category' => $dto->category,
            'updated_at' => now(),
            'created_at' => now(),
        ], $dtos);
    
      
        return DB::table('articles')->upsert(
            $data,
            ['url'], 
            ['title', 'description', 'updated_at']
        );
    }
}