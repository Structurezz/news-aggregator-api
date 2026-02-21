<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->string('title', 1024);
            $table->text('description')->nullable();
            $table->mediumText('content')->nullable();
            $table->string('author', 255)->nullable()->index();
            $table->string('source_name', 120)->index();
            $table->string('source_id', 100)->nullable()->index();
            $table->string('category', 100)->nullable()->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('fetched_at')->useCurrent();
            $table->string('url', 2048);
            $table->string('image_url', 2048)->nullable();
            $table->string('slug', 255)->nullable()->unique();
            $table->json('meta')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['source_name', 'published_at']);
            $table->index(['category', 'published_at']);
            $table->index(['author', 'published_at']);
        });

       
        DB::statement('ALTER TABLE articles ADD UNIQUE INDEX articles_url_unique (url(191))');
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};