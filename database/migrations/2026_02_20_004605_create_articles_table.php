<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Make URL and image_url longer to avoid "Data too long" errors
            $table->string('url', 2048)->change();        // allow very long URLs
            $table->string('image_url', 2048)->nullable()->change(); // safe for long image URLs
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('url', 255)->change();
            $table->string('image_url', 255)->nullable()->change();
        });
    }
};
