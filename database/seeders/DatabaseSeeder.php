<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\City;
use App\Models\Course;
use App\Models\Level;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->truncateSeededTables();

        $this->call([
            AdminSeeder::class,
            CitySeeder::class,
            CategorySeeder::class,
            LevelSeeder::class,
            CourseSeeder::class,
        ]);
    }

    private function truncateSeededTables(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('course_level')) {
            DB::table('course_level')->truncate();
        }
        if (Schema::hasTable('levels')) {
            Level::query()->truncate();
        }
        Course::query()->truncate();
        Category::query()->truncate();
        City::query()->truncate();
        Admin::query()->truncate();

        Schema::enableForeignKeyConstraints();
    }
}
