<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('url_hash', 64)->nullable()->after('url');
            $table->index('url_hash');
        });

    
        DB::statement('UPDATE articles SET url_hash = SHA2(url, 256) WHERE url IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('url_hash');
        });
    }
};