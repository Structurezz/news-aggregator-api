<?php

namespace App\Contracts;

use App\DTOs\ArticleDto;

interface NewsFetcher
{
    /**
     * Fetch recent articles from this source
     *
     * @param int $limit
     * @param string|null $keyword
     * @return ArticleDto[]
     */
    public function fetchRecent(int $limit = 30, ?string $keyword = null): array;
}