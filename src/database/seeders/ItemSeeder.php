<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::pluck('id')->toArray(); // 全カテゴリーのIDを取得

        Item::factory()->count(20)->create()->each(function ($item) use ($categories) {
            // ランダムに1〜3個のカテゴリーを選んで紐づけ
            $randomCategoryIds = collect($categories)->random(rand(1, 3))->all();
            $item->categories()->attach($randomCategoryIds); // 中間テーブルに紐づけ
        });
    }
}
