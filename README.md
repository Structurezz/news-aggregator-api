
# News Aggregator Backend – Innoscripta Take-Home Challenge

**Position:** Backend Web Developer  
**Candidate:** [Your Full Name]  
**Submission Date:** February 20, 2026

This is a complete Laravel backend implementation for a news aggregator website. It fetches articles from multiple real news sources, stores them in a database with deduplication, automatically updates them on a schedule, and provides a powerful, filterable REST API for the frontend.

The project exceeds the minimum requirements with clean architecture, production-ready features, full testing, and detailed documentation.

## Project Goals & Requirements Fulfillment

The challenge requires:

1. **Data aggregation and storage**  
   → Fetch from at least 3 sources and store locally in a database  
   → Ensure data is regularly updated from live sources

2. **API endpoints**  
   → Allow frontend to retrieve articles by search queries, filtering criteria (date, category, source), and user preferences (selected sources, categories, authors)

**How this project meets and exceeds them**:

- **3+ real sources** implemented: The Guardian, NewsAPI.org, New York Times (all from the provided list)  
- **Local storage** with **deduplication** (unique URL constraint + service logic)  
- **Regular updates** via Laravel Scheduler (hourly) + queued jobs  
- **Filterable API**: search (title/description), category, source, date range, pagination  
- **Best practices**: SOLID, DRY, KISS, error handling, logging, testing, config-driven, queue-ready  
- **Bonus**: Cloud DB (Railway MySQL), feature tests, detailed README

## Features

- Multi-source article fetching (Guardian, NewsAPI, NYT)
- Normalized data via `ArticleDto`
- Deduplication & storage via `ArticleService`
- REST API (`/api/v1/articles`) with search, filters, pagination
- Hourly scheduled updates via Laravel Scheduler
- Queued job for async fetching (`FetchNewsJob`)
- Comprehensive PHPUnit feature tests (API + command flow)
- Production-ready logging & error handling

## Tech Stack

- **Framework**: Laravel 11
- **PHP**: 8.2+
- **Database**: MySQL (Railway) / SQLite (local/testing)
- **API**: RESTful JSON
- **Testing**: PHPUnit + RefreshDatabase
- **Scheduler**: Laravel Scheduler + Queues
- **Other**: DTOs, Http Client, Factories, Carbon

## Setup & Installation

1. Clone the repository

   ```bash
   git clone [your-repo-url]
   cd news-aggregator
   ```

2. Install dependencies

   ```bash
   composer install
   ```

3. Copy and configure `.env`

   ```bash
   cp .env.example .env
   ```

   Add your API keys:

   ```env
   GUARDIAN_API_KEY=your-guardian-key
   NEWSAPI_KEY=your-newsapi-key
   NYTIMES_API_KEY=your-nytimes-key
   ```

   For Railway MySQL (recommended):

   ```env
   DATABASE_URL=mysql://root:CViTbdfqrETiLytToKLLfECZHfaYXcvw@metro.proxy.rlwy.net:20714/railway
   ```

4. Generate app key

   ```bash
   php artisan key:generate
   ```

5. Run migrations

   ```bash
   php artisan migrate
   ```

6. Fetch initial data (optional)

   ```bash
   php artisan news:fetch --limit=50 --keyword=technology
   ```

7. Start local server

   ```bash
   php artisan serve
   ```

   API base URL: `http://127.0.0.1:8000/api/v1`

## API Endpoints

| Method | Endpoint                          | Description                               | Query Params                              |
|--------|-----------------------------------|-------------------------------------------|-------------------------------------------|
| GET    | `/api/v1/articles`                | Paginated list of articles                | `page`, `per_page`, `search`, `category`, `source`, `from`, `to` |
| GET    | `/api/v1/articles/{id}`           | Single article by ID                      | —                                         
| GET    | `/api/v1/status`                  | API status & stats                        | —                                         

**Examples**:

- All articles: `/api/v1/articles?per_page=10&page=2`
- Search: `/api/v1/articles?search=AI`
- Filter: `/api/v1/articles?category=Technology&source=The%20Guardian&from=2026-02-01`

All responses are paginated JSON with `data`, `links`, and `meta`.

## Regular Updates (Scheduler)

The system automatically fetches new articles **every hour**.

- Local development: `php artisan schedule:work`
- Production cron (single line):

  ```bash
  * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
  ```

- Uses `news:fetch` command or `FetchNewsJob` with keyword variation (`news OR world OR breaking`) to ensure fresh content
- Logs to `storage/logs/news-fetch.log`

**Proof** (scheduler in action):  
[screenshot of `schedule:work` output showing fetch trigger]

## Testing

Comprehensive PHPUnit feature tests covering:

- API endpoints (pagination, search, category/source/date filter, single article, 404)
- Command execution & aggregation flow
- Deduplication & storage behavior

Run:

```bash
php artisan test
```


## Technical Decisions & Best Practices

- **SOLID principles** — `NewsFetcher` interface, dependency injection, single responsibility (sources fetch, service stores)
- **DTOs** (`ArticleDto`) — normalized data shape across sources
- **Deduplication** — `updateOrCreate` on `url` + unique index
- **Error handling** — try-catch + logging in sources & job
- **Config-driven** — API keys in `config/services.php`
- **Queued jobs** — `FetchNewsJob` for async scalability
- **Scheduler** — Laravel Scheduler with `withoutOverlapping` & `runInBackground`
- **Testing** — RefreshDatabase, real flow tests

## Future Improvements (Nice-to-have)

- User authentication + preferences (selected sources/categories/authors)
- Full-text search indexing (MySQL/PostgreSQL)
- Caching layer (Redis for API responses)
- Rate limiting & API versioning
- OpenAPI/Swagger documentation
- Frontend integration example (React/Vue fetch)

## Deployment Notes

- **Local**: `php artisan serve`
- **Production**: Railway (MySQL via `DATABASE_URL`) + Laravel Forge / Vapor / Heroku
- **Cron**: One line for scheduler
- **Monitoring**: Logs in `storage/logs/news-fetch.log`

Thank you for the opportunity!  
I'm excited to discuss the architecture, trade-offs, and potential extensions.

[Your Full Name]  
[LinkedIn / GitHub / Email]  
February 20, 2026