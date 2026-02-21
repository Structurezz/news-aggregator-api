<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Services\AggregatorService;
use App\Services\ArticleService;
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

        $this->app->bind('fake.news.fetcher', function () {
            return new class implements NewsFetcher {
                public function fetchRecent(int $limit = 30, ?string $keyword = null): array
                {
                    return [
                      
                        new ArticleDto(
                            title: 'Test Article 1',
                            url: 'https://example.com/1',
                            published_at: now(),
                            source_name: 'Test Source',
                            description: 'Description 1',
                            content: 'Content 1',
                            image_url: 'https://example.com/img1.jpg',
                            author: 'Author 1',
                            category: 'Technology',
                        ),
                        new ArticleDto(
                            title: 'Test Article 2',
                            url: 'https://example.com/2',
                            published_at: now(),
                            source_name: 'Test Source',
                            description: 'Description 2',
                            content: 'Content 2',
                            image_url: 'https://example.com/img2.jpg',
                            author: 'Author 2',
                            category: 'Technology',
                        ),
                    ];
                }
            };
        });

        $this->app->tag('fake.news.fetcher', 'news-fetchers');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_fetch_and_store_articles_via_command(): void
    {
    
        $articleService = app(ArticleService::class);
        $initialCount = Article::count();

        $dto = new ArticleDto(
            title: 'Brand New Unique Title ' . uniqid(), 
            url: 'https://unique-url.com/' . uniqid(),
            published_at: now(),
            source_name: 'Test'
        );
        
        $articleService->storeMany([$dto]);

        $this->assertGreaterThan($initialCount, Article::count());

    
        $currentCount = Article::count();

   
        $aggregator = new AggregatorService(iterator_to_array(app()->tagged('news-fetchers')));
        $articles = $aggregator->aggregate(2);

        foreach ($articles as $item) {
            $articleService->storeMany([$item]);
        }

        $this->assertGreaterThan($currentCount, Article::count());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function aggregator_service_stores_new_articles(): void
    {
        $aggregator = new AggregatorService(iterator_to_array(app()->tagged('news-fetchers')));
        $articleService = app(ArticleService::class);

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
