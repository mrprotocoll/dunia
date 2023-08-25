<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name' => fake()->title(),
            'images' => [], // Fill this with appropriate image URLs or data
            'price' => fake()->randomFloat(2, 10, 100),
            'print_price' => fake()->randomFloat(2, 5, 50),
            'description' => fake()->paragraph,
            'author_id' => Author::factory(), // Assuming authors have IDs from 1 to 10
            'age_range_id' => fake()->numberBetween(1, 5), // Assuming age_ranges have IDs from 1 to 5
            'product_file' => 'path/to/your/product.pdf',
            'preview' => 'path/to/your/preview.pdf',
            'weight' => fake()->randomFloat(2, 0.1, 10),
        ];
    }
}
