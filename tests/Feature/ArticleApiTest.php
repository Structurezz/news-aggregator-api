<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

    
        Article::factory()->count(15)->create([
            'published_at' => fn() => now()->subDays(rand(1, 10)),
            'source_name' => 'The Guardian',
            'category' => 'Technology',
        ]);

        Article::factory()->count(5)->create([
            'published_at' => fn() => now()->subDays(rand(1, 10)),
            'source_name' => 'BBC News',
            'category' => 'Politics',
        ]);
    }


    public function it_returns_paginated_list_of_articles()
    {
        $response = $this->getJson('/api/v1/articles?page=1&per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'url',
                        'image_url',
                        'published_at',
                        'source',
                        'author',
                        'category',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(10, 'data');
    }

    public function it_supports_full_text_search()
    {
        Article::factory()->create([
            'title' => 'AI Breakthrough in 2026',
            'description' => 'New AI model changes everything',
        ]);

        $response = $this->getJson('/api/v1/articles?search=AI');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'AI Breakthrough in 2026']);
    }

 
    public function it_filters_articles_by_category()
    {
        $response = $this->getJson('/api/v1/articles?category=Technology&per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['category' => 'Technology']);
    }

    public function it_filters_articles_by_source()
    {
        $response = $this->getJson('/api/v1/articles?source=The Guardian&per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['source' => 'The Guardian']);
    }

  
    public function it_filters_articles_by_date_range()
    {
        $from = now()->subDays(5)->format('Y-m-d');
        $to = now()->subDays(3)->format('Y-m-d');

        $response = $this->getJson("/api/v1/articles?from={$from}&to={$to}");

        $response->assertStatus(200);

        $dates = collect($response->json('data'))->pluck('published_at')->map(fn($d) => substr($d, 0, 10));

        foreach ($dates as $date) {
            $this->assertGreaterThanOrEqual($from, $date);
            $this->assertLessThanOrEqual($to, $date);
        }
    }


    public function it_returns_single_article_by_id()
    {
        $article = Article::factory()->create(['title' => 'Test Single Article']);

        $response = $this->getJson("/api/v1/articles/{$article->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $article->id,
                'title' => 'Test Single Article',
            ]);
    }


    public function it_returns_404_for_non_existing_article()
    {
        $response = $this->getJson('/api/v1/articles/999999999');

        $response->assertStatus(404);
    }


    public function it_supports_custom_per_page_limit()
    {
        $response = $this->getJson('/api/v1/articles?per_page=7');

        $response->assertStatus(200)
            ->assertJsonCount(7, 'data');
    }
}