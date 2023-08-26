<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tag = Tag::factory()->create();
        Product::factory()
            ->hasAttached($tag)
            ->hasAttached(Category::factory(2))
            ->hasImages(2, [
                'image' => fake()->imageUrl
            ])
            ->create();
    }
}
