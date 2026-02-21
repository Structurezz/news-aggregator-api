<?php

namespace Tests\Feature;

use App\Jobs\FetchNewsJob;
use App\Models\Article;
use App\Contracts\NewsFetcher;
use App\DTOs\ArticleDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NewsApiFilteringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Mocks for the Fetch Job
        $newsApiMock = $this->createMock(NewsFetcher::class);
        $newsApiMock->method('fetchRecent')->willReturn([
            new ArticleDto(title: 'Tech News A', url: 'https://a.com', published_at: now(), source_name: 'TechCrunch'),
            new ArticleDto(title: 'Politics B', url: 'https://b.com', published_at: now(), source_name: 'CNN'),
        ]);

        $guardianMock = $this->createMock(NewsFetcher::class);
        $guardianMock->method('fetchRecent')->willReturn([
            new ArticleDto(title: 'Guardian Tech C', url: 'https://c.com', published_at: now(), source_name: 'The Guardian', category: 'technology'),
        ]);

        $nytMock = $this->createMock(NewsFetcher::class);
        $nytMock->method('fetchRecent')->willReturn([
            new ArticleDto(title: 'NYT Politics D', url: 'https://d.com', published_at: now(), source_name: 'New York Times', category: 'politics'),
        ]);

        // 2. Bind and Tag
        $this->app->bind('mock.newsapi', fn() => $newsApiMock);
        $this->app->bind('mock.guardian', fn() => $guardianMock);
        $this->app->bind('mock.nyt', fn() => $nytMock);

        $this->app->tag(['mock.newsapi', 'mock.guardian', 'mock.nyt'], 'news-fetchers');
    }

    #[Test]
    public function end_to_end_fetch_store_and_filter_by_source()
    {
        // Executes the job which uses the mocks defined in setUp
        dispatch_sync(new FetchNewsJob());

        $this->assertDatabaseCount('articles', 4);

        // FIX: Spatie QueryBuilder expects ?filter[source_name]= or ?filter[source]=
        // based on your AllowedFilter mapping in GetArticlesAction
        $response = $this->getJson('/api/v1/articles?filter[source]=TechCrunch');

        $response->assertOk()
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.title', 'Tech News A');
    }

    #[Test]
    public function api_filters_by_search_term_across_fields()
    {
        Article::factory()->create([
            'title' => 'Artificial Intelligence Breakthrough',
            'source_name' => 'The Guardian',
        ]);

        // FIX: Use filter[search]
        $response = $this->getJson('/api/v1/articles?filter[search]=artificial');

        $response->assertOk()
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.title', 'Artificial Intelligence Breakthrough');
    }

    #[Test]
    public function api_supports_date_range_filtering()
    {
        Article::factory()->create(['published_at' => now()->subDays(10), 'title' => 'Old Article']);
        Article::factory()->create(['published_at' => now(), 'title' => 'Recent Article']);

        // FIX: Use filter[from]
        $from = now()->subDays(3)->format('Y-m-d');
        $response = $this->getJson("/api/v1/articles?filter[from]={$from}");

        $response->assertOk()
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.title', 'Recent Article');
    }

    #[Test]
    public function api_returns_filter_options_for_frontend_dropdowns()
    {
        Article::factory()->create(['source_name' => 'The Guardian', 'category' => 'technology']);
        Article::factory()->create(['source_name' => 'New York Times', 'category' => 'politics']);

        $response = $this->getJson('/api/v1/articles');

        $response->assertOk()
                 ->assertJsonStructure([
                     'data',
                     'pagination',
                     'filters' => ['sources', 'categories'],
                 ])
                 ->assertJsonCount(2, 'filters.sources')
                 ->assertJsonCount(2, 'filters.categories');
    }

    #[Test]
    public function handles_no_results_gracefully()
    {
        $response = $this->getJson('/api/v1/articles?filter[source]=NonExistentSource');

        $response->assertOk()
                 ->assertJsonCount(0, 'data');
    }
}