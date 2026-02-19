# News Aggregator Backend – Innoscripta Take-Home Challenge

![Innoscripta](https://via.placeholder.com/1200x300/0d47a1/ffffff?text=Innoscripta+News+Aggregator+Backend)

Clean, SOLID-compliant Laravel backend that aggregates news from multiple sources and exposes a filtered/searchable JSON:API.

## Features

- Aggregates articles from **NewsAPI.org**, **The Guardian**, **New York Times**
- Normalized data model using DTOs (spatie/laravel-data)
- Console command: `php artisan news:fetch`
- Automatic hourly fetching via Laravel scheduler
- Full-text searchable API endpoint `/api/articles`
- Filtering by: search term, source, category, author, date range
- Deduplication based on article URL
- Proper error handling & logging
- Ready for tests (example test included)

## Tech Stack

- PHP 8.2+
- Laravel 11 / 12
- SQLite (easy local setup) or MySQL/PostgreSQL
- Guzzle HTTP client
- spatie/laravel-data (DTOs)

## Installation (when you have PHP & Composer)

```bash
composer install
cp .env.example .env
# → add your API keys to .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate