<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
      
        $indexExists = DB::selectOne(
            "SELECT COUNT(*) as cnt 
             FROM information_schema.statistics 
             WHERE table_schema = DATABASE() 
             AND table_name = 'articles' 
             AND index_name = 'articles_url_unique'"
        )->cnt;

        if ($indexExists > 0) {
            DB::statement('ALTER TABLE articles DROP INDEX articles_url_unique');
        }

      
        DB::statement('ALTER TABLE articles ADD UNIQUE INDEX articles_url_unique (url(191))');
    }

    public function down(): void
    {
       
        $indexExists = DB::selectOne(
            "SELECT COUNT(*) as cnt 
             FROM information_schema.statistics 
             WHERE table_schema = DATABASE() 
             AND table_name = 'articles' 
             AND index_name = 'articles_url_unique'"
        )->cnt;

        if ($indexExists > 0) {
            DB::statement('ALTER TABLE articles DROP INDEX articles_url_unique');
        }
    }
};