<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's product categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Laptops',
                'description' => 'Portable computers including ultrabooks, workstations, and gaming laptops.',
            ],
            [
                'name' => 'Smartphones',
                'description' => 'Mobile phones, from flagship models to budget devices.',
            ],
            [
                'name' => 'Monitors & Displays',
                'description' => 'Computer monitors, professional displays, and external screens.',
            ],
            [
                'name' => 'Peripherals',
                'description' => 'Input devices such as keyboards, mice, and related accessories.',
            ],
            [
                'name' => 'Audio',
                'description' => 'Headphones, earbuds, speakers, and audio equipment.',
            ],
            [
                'name' => 'Storage & Networking',
                'description' => 'SSDs, storage drives, routers, and networking hardware.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
