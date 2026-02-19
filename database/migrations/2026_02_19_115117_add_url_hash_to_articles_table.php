<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('image_url', 2048)->nullable()->change();
        });
        
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Revert back to 255 chars
            $table->string('image_url', 255)->change();
        });
    }
};
