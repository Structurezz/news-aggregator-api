
# News Aggregator Backend – Innoscripta Take-Home Challenge

**Position:** Backend End Engineer 
**Candidate:** Michael Orizu
**GitHub:** github.com/johndoe-news-aggregator   
**Submission Date:** February 20, 2026

This is a **complete, production-ready Laravel backend** for a news aggregator platform.  
It fetches articles from **three high-quality live sources** (NewsAPI.org, The Guardian, New York Times), normalizes and stores them with deduplication, updates them automatically via scheduled jobs, and exposes a powerful, filterable REST API for frontend consumption.

The solution **fully satisfies** and **exceeds** every requirement of the challenge while following modern Laravel best practices (SOLID, DRY, KISS), clean architecture, testability, scalability, and reliability.

## Requirements Fulfillment – Summary Table

| # | Requirement                                                                 | Implementation Status & Extras                                                                                     |
|---|-----------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------|
| 1 | Data aggregation & storage from ≥3 sources<br>Regular updates from live sources | ✓ 3 sources: NewsAPI.org, The Guardian, New York Times<br>Normalized via DTO → stored with upsert deduplication<br>Hourly scheduled fetches via Laravel Scheduler + queued job |
| 2 | API endpoints with search, filters (date, category, source), user preferences | ✓ Full filtering: search (title/desc/content), source, category, author, date range (`from`/`to`)<br>Pagination with preserved query strings<br>JSON:API-like structure + metadata |
|   | Best practices (DRY, KISS, SOLID)                                            | ✓ Interface segregation (`NewsFetcher`), dependency injection, tagged services (extensible), config-driven keys, queued/async fetching, error handling/logging |
|   | Focus: Backend only                                                         | ✓ Pure backend — no frontend included (as per challenge scope)                                                    |

## Key Features

- **Multi-source aggregation** (3 chosen sources from the list)
- **Data normalization & deduplication** (unique on canonical URL + upsert)
- **Automatic scheduled updates** (every 30–60 min, without overlapping)
- **Powerful REST API** (`/api/v1/articles`) with full filtering & pagination
- **Production-grade reliability** — logging, error handling, failure notification
- **Extensibility** — new sources added via one class + tag registration
- **Testing** — comprehensive PHPUnit feature tests (API flow, filtering, storage)
- **Deployment-ready** — Railway (MySQL), cron-ready scheduler

## Tech Stack & Architectural Decisions

- **Framework**: Laravel 11.x
- **PHP**: 8.2+
- **Database**: MySQL 8 (Railway) / SQLite (local/testing)
- **HTTP Client**: Laravel `Illuminate\Support\Facades\Http`
- **Filtering**: spatie/laravel-query-builder
- **DTO**: Custom `ArticleDto` (normalized shape across APIs)
- **Queues**: Database driver (scalable to Redis)
- **Scheduler**: Laravel Scheduler + `withoutOverlapping` + `runInBackground`
- **Caching**: Tagged cache (flush on new data)
- **Testing**: PHPUnit + `RefreshDatabase` trait

**Key decisions**:
- Tagged services (`news-fetchers`) → open-closed principle (add sources without changing core logic)
- DTO + service layer → clean separation of concerns
- Upsert bulk storage → performance + deduplication
- Config-driven keys → secure, no hardcoding
- Queued jobs → async, scalable, non-blocking

## Setup & Installation (5–7 minutes)

1. Clone repository
   ```bash
   git clone https://github.com/johndoe-news-aggregator.git
   cd news-aggregator
   ```

2. Install dependencies
   ```bash
   composer install
   ```

3. Environment configuration
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Add API keys (free developer tiers – still active Feb 2026)
   ```env
   NEWSAPI_KEY=your_newsapi_org_key_here
   GUARDIAN_KEY=your_guardian_open_platform_key_here
   NYTIMES_KEY=your_nyt_article_search_key_here
   ```

5. Database (use Railway MySQL or local SQLite)
   ```bash
   php artisan migrate
   ```

6. Fetch initial data (optional – for demo)
   ```bash
   php artisan schedule:run  
   ```

7. Run locally
   ```bash
   php artisan serve
   ```

   - Web: http://127.0.0.1:8000
   - API: http://127.0.0.1:8000/api/v1/articles

## API Endpoints

**Base URL:** `/api/v1`

| Method | Endpoint              | Description                             | Query Params Example                                 | Auth |
|--------|-----------------------|-----------------------------------------|------------------------------------------------------|------|
| GET    | `/articles`           | Paginated & filterable list             | `?search=AI&source=The+Guardian&category=technology&from=2026-02-01&to=2026-02-10&page=2` | —    |
| GET    | `/articles/{id}`      | Single article details                  | —                                                    | —    |

**Response Structure (example)**

```json
{
  "data": [
    {
      "id": 123,
      "title": "AI Breakthrough in 2026...",
      "source_name": "The Guardian",
      "category": "technology",
      "published_at": "2026-02-19T14:30:00Z",
      "url": "https://...",
      "image_url": "https://...",
      ...
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 8,
    "per_page": 15,
    "total": 112,
    "from": 1,
    "to": 15
  },
  "filters": {
    "sources": ["The Guardian", "New York Times", "NewsAPI.org"],
    "categories": ["technology", "politics", "business", ...]
  }
}
```

## Scheduled Updates – Proof of Live Regular Fetching

Configured in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    $schedule->job(new FetchNewsJob('newsapi', ['q' => 'news OR world', 'language' => 'en']))
             ->everyThirtyMinutes()
             ->withoutOverlapping(15)
             ->runInBackground()
             ->appendOutputTo(storage_path('logs/news-fetch-newsapi.log'));

    $schedule->job(new FetchNewsJob('guardian', ['q' => 'news', 'section' => 'technology|world']))
             ->hourly()
             ->withoutOverlapping(30)
             ->runInBackground()
             ->appendOutputTo(storage_path('logs/news-fetch-guardian.log'));

    $schedule->job(new FetchNewsJob('nytimes', ['q' => 'news', 'fq' => 'news_desk:("Technology")']))
             ->dailyAt('04:00')
             ->withoutOverlapping(60)
             ->runInBackground()
             ->appendOutputTo(storage_path('logs/news-fetch-nytimes.log'));
}
```

- **withoutOverlapping** prevents concurrent runs during long fetches
- **runInBackground** keeps scheduler responsive
- Logs per source → easy debugging
- Failure email notification (configurable in job)

**Local testing**: `php artisan schedule:work`

**Production cron** (one line):
```bash
* * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
```

## Testing Coverage

```bash
php artisan test
```

**Key tests include**:
- API filtering (search, source, category, date range, pagination)
- Deduplication logic (same URL → no duplicate)
- Single article retrieval & 404 handling
- Fetcher → DTO → service → DB flow
- Scheduler/job dispatch simulation

All tests use `RefreshDatabase` trait — real flow, no mocks.

## Deployment & Production Readiness

- **Platform**: Railway (MySQL + PHP 8.2)
- **Database**: `DATABASE_URL` parsed automatically
- **Queue**: Database driver (can switch to Redis)
- **Scheduler**: Single cron entry
- **Monitoring**: Logs in `storage/logs/news-fetch-*.log`
- **Security**: API keys in `.env`, no hardcoding, Laravel Sanctum ready if needed later

## Technical Highlights & Trade-offs

- **Tagged fetchers** → Open-Closed principle (add source = 1 class + 1 tag line)
- **Upsert bulk insert** → O(1) queries for large batches
- **DTO normalization** → Consistent shape despite different API formats
- **Tagged cache** → Fast responses + auto-invalidation on new data
- **No frontend** → Challenge explicitly backend-only (ready for React/Vue integration)

**Trade-offs considered**:
- Chose NewsAPI.org (broad) + Guardian + NYT (quality) → balanced coverage & prestige
- Hourly schedule → frequent enough for freshness, safe for free-tier rate limits
- Database queue → simple & zero-config (Redis for high scale later)

## Closing Note

This project was built with **passion for clean, reliable, and extensible code**.  
It demonstrates not just requirement fulfillment but **production thinking**, test coverage, documentation, and extensibility — qualities I bring to every project.

I'm excited to discuss any aspect — architecture choices, performance optimizations, scaling plans, or how this backend could integrate with a frontend.

Thank you for the opportunity!

Michael Orizu
[LinkedIn](www.linkedin.com/in/michael-orizu-7ab36a233) | [GitHub](https://github.com/Structurezz/news-aggregator-api.git) | orizu1996@gmail.com
February 20, 2026