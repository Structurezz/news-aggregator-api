<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {

            if (!Schema::hasColumn('articles', 'url_hash')) {
                $table->string('url_hash', 64)->nullable()->unique()->after('url');
            }

        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'url_hash')) {
                $table->dropColumn('url_hash');
            }
        });
    }
};
