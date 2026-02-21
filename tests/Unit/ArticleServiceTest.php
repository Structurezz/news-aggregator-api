<?php

namespace Tests\Unit;

use App\DTOs\ArticleDto;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ArticleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ArticleService::class);
    }

    /** @test */

public function it_stores_new_article_and_skips_duplicate_by_url()
{
   
    $dto = new ArticleDto(
        title: 'Unique Article',
        url: 'https://example.com/unique',
        published_at: now(),
        source_name: 'Test'
    );

    $this->service->storeMany([$dto]);
    $this->assertDatabaseCount('articles', 1);
}

/** @test */
public function it_handles_bulk_upsert_with_mixed_new_and_existing()
{
    Article::factory()->create(['url' => 'https://example.com/existing']);

    $dtos = [
        new ArticleDto(title: 'Updated', url: 'https://example.com/existing', published_at: now(), source_name: 'Test'),
        new ArticleDto(title: 'New 1', url: 'https://example.com/new1', published_at: now(), source_name: 'Test'),
        new ArticleDto(title: 'New 2', url: 'https://example.com/new2', published_at: now(), source_name: 'Test'),
    ];

    $this->service->storeMany($dtos);
    $this->assertDatabaseCount('articles', 3);
}
}