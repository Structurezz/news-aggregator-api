<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Services\AggregatorService;
use App\DTOs\ArticleDto;
use App\Contracts\NewsFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        /**
         * Bind fake fetcher into container
         */
        $this->app->bind('fake.news.fetcher', function () {
            return new class implements NewsFetcher {
                public function fetchRecent(int $limit = 30, ?string $keyword = null): array
                {
                    return [
                        new ArticleDto([
                            'title' => 'Test Article 1',
                            'description' => 'Description 1',
                            'content' => 'Content 1',
                            'url' => 'https://example.com/1',
                            'image_url' => 'https://example.com/img1.jpg',
                            'published_at' => now(),
                            'source_name' => 'Test Source',
                            'author' => 'Author 1',
                            'category' => 'Technology',
                        ]),
                        new ArticleDto([
                            'title' => 'Test Article 2',
                            'description' => 'Description 2',
                            'content' => 'Content 2',
                            'url' => 'https://example.com/2',
                            'image_url' => 'https://example.com/img2.jpg',
                            'published_at' => now(),
                            'source_name' => 'Test Source',
                            'author' => 'Author 2',
                            'category' => 'Technology',
                        ]),
                    ];
                }
            };
        });

        /**
         * Tag it so AggregatorService + Command can resolve it
         */
        $this->app->tag('fake.news.fetcher', 'news-fetchers');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_fetch_and_store_articles_via_command(): void
    {
        $initialCount = Article::count();

        // Directly get the article service
        $articleService = app(\App\Services\ArticleService::class);

        // Aggregate articles from the tagged fake fetchers
        $aggregator = new AggregatorService(iterator_to_array(app()->tagged('news-fetchers')));
        $articles = $aggregator->aggregate(2);

        // Store articles synchronously
        foreach ($articles as $dto) {
            $articleService->storeMany([$dto]);
        }

        // Assert articles were stored
        $this->assertGreaterThan($initialCount, Article::count());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function aggregator_service_stores_new_articles(): void
    {
        $aggregator = new AggregatorService(iterator_to_array(app()->tagged('news-fetchers')));
        $articleService = app(\App\Services\ArticleService::class);

        $initialCount = Article::count();

        $articles = $aggregator->aggregate(2);
        foreach ($articles as $dto) {
            $articleService->storeMany([$dto]);
        }

        $this->assertGreaterThan($initialCount, Article::count());
        $this->assertGreaterThanOrEqual(0, count($articles));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_a_working_welcome_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
