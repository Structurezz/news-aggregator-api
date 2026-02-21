<?php

namespace Tests\Unit;

use App\DTOs\ArticleDto;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArticleDtoTest extends TestCase
{
    #[Test]
    public function it_creates_from_named_parameters(): void
    {
        $dto = new ArticleDto(
            title: 'Test Title',
            url: 'https://example.com',
            published_at: Carbon::now(),
            source_name: 'Test Source',
            description: 'Short desc',
            content: 'Full content',
            image_url: 'https://example.com/img.jpg',
            author: 'Jane Doe',
            category: 'Technology'
        );

        $this->assertEquals('Test Title', $dto->title);
        $this->assertEquals('https://example.com', $dto->url);
        $this->assertInstanceOf(Carbon::class, $dto->published_at);
    }

    #[Test]
    public function it_allows_null_for_optional_fields(): void
    {
        $dto = new ArticleDto(
            title: 'Minimal',
            url: 'https://test.com',
            published_at: now(),
            source_name: 'Minimal Source'
        );

        $this->assertNull($dto->description);
        $this->assertNull($dto->content);
        $this->assertNull($dto->author);
        $this->assertNull($dto->image_url);
        $this->assertNull($dto->category);
    }

    #[Test]
    public function it_throws_type_error_on_missing_required_fields(): void
    {
        $this->expectException(\TypeError::class);

        new ArticleDto(
            title: 'Missing URL',
            published_at: now(),
            source_name: 'Test'
        );
    }
}