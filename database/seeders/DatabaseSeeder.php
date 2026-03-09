<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\City;
use App\Models\Course;
use Illuminate\Database\Seeder;
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
            CourseSeeder::class,
        ]);
    }

    private function truncateSeededTables(): void
    {
        Schema::disableForeignKeyConstraints();

        Course::query()->truncate();
        Category::query()->truncate();
        City::query()->truncate();
        Admin::query()->truncate();

        Schema::enableForeignKeyConstraints();
    }
}
