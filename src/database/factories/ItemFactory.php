<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'title' => $this->faker->word(),
            'brand_id' => Brand::inRandomOrder()->value('id'),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(300, 50000),
            'category_id' => Category::inRandomOrder()->value('id'),
            'likes_count' => 0,
            'comments_count' => 0,
            'condition' => $this->faker->numberBetween(1, 4),
            'status' => 'available',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
