<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            // 'name' => $this->faker->realText(20), // ユーザー名：20文字以内
            'name' => Str::limit($this->faker->name(), 20),
            'email' => $this->faker->unique()->safeEmail(), // メール形式
            'password' => bcrypt('password123'), // 8文字以上
            'profile_completed' => $this->faker->boolean(),
            'profile_image_url' => $this->faker->imageUrl(640, 480, 'people', true, 'profile'), // 拡張子は後で調整可能
            'shipping_address' => $this->faker->streetAddress(), // 住所：入力必須
            'postal_code' => $this->faker->regexify('[0-9]{3}-[0-9]{4}'), // 郵便番号：ハイフンあり8文字
            'building_name' => $this->faker->word(),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
