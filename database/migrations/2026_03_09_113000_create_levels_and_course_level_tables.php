<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('course_level', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('level_id')->constrained('levels')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['course_id', 'level_id']);
        });

        if (Schema::hasColumn('courses', 'level')) {
            $courseLevels = DB::table('courses')
                ->select('id', 'level')
                ->whereNotNull('level')
                ->get();

            foreach ($courseLevels as $courseLevel) {
                $rawLevel = trim((string) $courseLevel->level);
                if ($rawLevel === '') {
                    continue;
                }

                $normalizedName = Str::of($rawLevel)->replace(['-', '_'], ' ')->title()->toString();
                $slug = Str::slug($rawLevel);
                if ($slug === '') {
                    continue;
                }

                $levelId = DB::table('levels')->where('slug', $slug)->value('id');
                if ($levelId === null) {
                    $levelId = DB::table('levels')->insertGetId([
                        'name' => $normalizedName,
                        'slug' => $slug,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('course_level')->insertOrIgnore([
                    'course_id' => $courseLevel->id,
                    'level_id' => $levelId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('level');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('courses', 'level')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->string('level', 32)->nullable()->after('status');
            });
        }

        if (Schema::hasTable('course_level') && Schema::hasTable('levels')) {
            $mappings = DB::table('course_level')
                ->join('levels', 'levels.id', '=', 'course_level.level_id')
                ->select('course_level.course_id', 'levels.slug')
                ->get();

            foreach ($mappings as $mapping) {
                DB::table('courses')
                    ->where('id', $mapping->course_id)
                    ->update(['level' => $mapping->slug]);
            }
        }

        Schema::dropIfExists('course_level');
        Schema::dropIfExists('levels');
    }
};
