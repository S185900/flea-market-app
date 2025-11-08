<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;

class ItemImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // 'item_id' => Item::inRandomOrder()->first()->id ?? Item::factory(),
            // 'image_path' => $this->faker->imageUrl(),
            'item_id' => null, // テスト側で明示的に指定する前提
            'image_path' => 'items/' . $this->faker->uuid . '.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
