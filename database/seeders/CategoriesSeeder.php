<?php

// ============================================
// database/seeders/CategoriesSeeder.php
// ============================================

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Novel', 'description' => 'Narrative fiction works', 'display_order' => 1],
            ['name' => 'Poetry', 'description' => 'Poetic and lyrical works', 'display_order' => 2],
            ['name' => 'Short Story', 'description' => 'Short narratives', 'display_order' => 3],
            ['name' => 'Theater', 'description' => 'Theatrical plays and dramaturgy', 'display_order' => 4],
            ['name' => 'Chronicle', 'description' => 'Short texts about everyday life', 'display_order' => 5],
            ['name' => 'Essay', 'description' => 'Reflective and critical texts', 'display_order' => 6],
            ['name' => 'Memoirs', 'description' => 'Autobiographical accounts', 'display_order' => 7],
            ['name' => 'Children\'s Literature', 'description' => 'Works for children', 'display_order' => 8],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}





