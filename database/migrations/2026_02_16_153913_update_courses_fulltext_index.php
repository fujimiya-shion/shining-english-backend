<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropFullText(['name', 'slug']);
        });

        DB::statement('ALTER TABLE courses ADD FULLTEXT INDEX courses_name_slug_fulltext (name, slug) WITH PARSER ngram');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE courses DROP INDEX courses_name_slug_fulltext');

        Schema::table('courses', function (Blueprint $table) {
            $table->fullText(['name', 'slug']);
        });
    }
};
