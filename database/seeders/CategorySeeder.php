<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::truncate();
        $categories = [
            ['name' => 'Grammar Basics', 'slug' => 'grammar-basics'],
            ['name' => 'Vocabulary Expansion', 'slug' => 'vocabulary-expansion'],
            ['name' => 'Listening Mastery', 'slug' => 'listening-mastery'],
            ['name' => 'Speaking Fluency', 'slug' => 'speaking-fluency'],
            ['name' => 'Writing Practice', 'slug' => 'writing-practice'],
            ['name' => 'Exam Prep', 'slug' => 'exam-prep'],
        ];

        foreach ($categories as $data) {
            Category::query()->firstOrCreate(
                ['slug' => $data['slug']],
                array_merge(['parent_id' => null], $data)
            );
        }
    }
}
