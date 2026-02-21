<?php

namespace Tests\Feature;

use App\Jobs\FetchNewsJob;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NewsAggregationAndApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        // Mock real API responses for all 3 sources
        Http::fake([
            'newsapi.org/*' => Http::response([
                'articles' => [
                    [
                        'title' => 'AI Breakthrough 2026',
                        'description' => 'New model...',
                        'content' => 'Full text...',
                        'url' => 'https://newsapi.org/ai-breakthrough',
                        'urlToImage' => 'https://example.com/ai.jpg',
                        'publishedAt' => now()->toIso8601String(),
                        'author' => 'Jane Tech',
                        'source' => ['name' => 'TechCrunch'],
                    ],
                    // duplicate URL
                    [
                        'title' => 'Duplicate AI',
                        'url' => 'https://newsapi.org/ai-breakthrough',
                        'publishedAt' => now()->toIso8601String(),
                        'source' => ['name' => 'TechCrunch'],
                    ],
                ]
            ], 200),

            'content.guardianapis.com/*' => Http::response([
                'response' => [
                    'results' => [
                        [
                            'webTitle' => 'Guardian on AI Ethics',
                            'fields' => [
                                'trailText' => 'Ethical concerns...',
                                'bodyText' => 'Full article...',
                                'byline' => 'Guardian Staff',
                                'thumbnail' => 'https://guardian.com/ai.jpg',
                            ],
                            'webPublicationDate' => now()->toIso8601String(),
                            'webUrl' => 'https://theguardian.com/ai-ethics',
                            'sectionName' => 'Technology',
                        ],
                    ]
                ]
            ], 200),

            'api.nytimes.com/*' => Http::response([
                'response' => [
                    'docs' => [
                        [
                            'headline' => ['main' => 'NYT AI Regulation'],
                            'abstract' => 'New rules proposed...',
                            'byline' => ['original' => 'John Doe'],
                            'pub_date' => now()->toIso8601String(),
                            'web_url' => 'https://nytimes.com/ai-regulation',
                            'multimedia' => [['url' => 'https://nyt.com/ai.jpg']],
                        ],
                    ]
                ]
            ], 200),
        ]);
    }

    /** @test */
    public function fetch_job_stores_articles_from_all_sources_with_deduplication()
    {
        $initialCount = Article::count();

        dispatch_sync(new FetchNewsJob());

        $this->assertGreaterThan($initialCount, Article::count());

        // Expect 3 articles (1 per source, duplicate skipped)
        $this->assertEquals(3, Article::count());

        $this->assertDatabaseHas('articles', [
            'title' => 'AI Breakthrough 2026',
            'source_name' => 'TechCrunch',
            'url' => 'https://newsapi.org/ai-breakthrough',
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'Guardian on AI Ethics',
            'source_name' => 'The Guardian',
        ]);
    }

    /** @test */
    public function api_filters_articles_by_source_correctly()
    {
        // Seed test data
        Article::factory()->create(['source_name' => 'The Guardian', 'title' => 'Guardian Test']);
        Article::factory()->create(['source_name' => 'New York Times', 'title' => 'NYT Test']);

        $response = $this->getJson('/api/v1/articles?source=The+Guardian');

        $response->assertOk()
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.source_name', 'The Guardian')
                 ->assertJsonMissing(['New York Times']);
    }

    /** @test */
    public function api_supports_search_across_title_description_content()
    {
        Article::factory()->create([
            'title' => 'Artificial Intelligence Future',
            'description' => 'AI is transforming...',
            'content' => 'Deep learning models...',
        ]);

        $response = $this->getJson('/api/v1/articles?search=artificial');

        $response->assertOk()
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.title', 'Artificial Intelligence Future');
    }

    /** @test */
    public function api_filters_by_date_range()
    {
        Article::factory()->create(['published_at' => '2026-02-05', 'title' => 'Old News']);
        Article::factory()->create(['published_at' => '2026-02-18', 'title' => 'Recent News']);

        $response = $this->getJson('/api/v1/articles?from=2026-02-10');

        $response->assertOk()
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.title', 'Recent News');
    }

    /** @test */
    public function api_provides_dropdown_filter_options()
    {
        Article::factory()->count(2)->create(['source_name' => 'The Guardian', 'category' => 'technology']);
        Article::factory()->create(['source_name' => 'New York Times', 'category' => 'politics']);

        $response = $this->getJson('/api/v1/articles');

        $response->assertOk()
                 ->assertJsonStructure([
                     'data',
                     'pagination',
                     'filters' => [
                         'sources',
                         'categories',
                     ],
                 ])
                 ->assertJsonCount(2, 'filters.sources')
                 ->assertJsonCount(2, 'filters.categories');
    }

    /** @test */
    public function api_returns_empty_results_when_no_match()
    {
        $response = $this->getJson('/api/v1/articles?source=NonExistent');

        $response->assertOk()
                 ->assertJsonCount(0, 'data')
                 ->assertJsonPath('pagination.total', 0);
    }
}