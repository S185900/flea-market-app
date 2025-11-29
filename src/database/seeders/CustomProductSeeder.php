<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ItemImage;

class CustomProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIds = User::pluck('id')->toArray();
        $brandMap = Brand::pluck('id', 'brand_name')->toArray();
        $categoryMap = Category::pluck('id', 'category_name')->toArray();

        $conditionMap = [
            '良好' => 1,
            '目立った傷や汚れなし' => 2,
            'やや傷や汚れあり' => 3,
            '状態が悪い' => 4,
        ];

        $products = [
            [
                'title' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'Armani+Mens+Clock.jpg',
                'categories' => ['ファッション', 'メンズ', 'アクセサリー'],
                'condition' => '良好',
            ],
            [
                'title' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'HDD+Hard+Disk.jpg',
                'categories' => ['家電'],
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'title' => '玉ねぎ3束',
                'price' => 300,
                'brand' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'iLoveIMG+d.jpg',
                'categories' => ['ハンドメイド'],
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'title' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'Leather+Shoes+Product+Photo.jpg',
                'categories' => ['ファッション', 'メンズ'],
                'condition' => '状態が悪い',
            ],
            [
                'title' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'image' => 'Living+Room+Laptop.jpg',
                'categories' => ['家電'],
                'condition' => '良好',
            ],
            [
                'title' => 'マイク',
                'price' => 8000,
                'brand' => null,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'Music+Mic+4632231.jpg',
                'categories' => ['家電'],
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'title' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'Purse+fashion+pocket.jpg',
                'categories' => ['ファッション', 'レディース'],
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'title' => 'タンブラー',
                'price' => 500,
                'brand' => null,
                'description' => '使いやすいタンブラー',
                'image' => 'Tumbler+souvenir.jpg',
                'categories' => ['キッチン'],
                'condition' => '状態が悪い',
            ],
            [
                'title' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image' => 'Waitress+with+Coffee+Grinder.jpg',
                'categories' => ['キッチン'],
                'condition' => '良好',
            ],
            [
                'title' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'image' => '外出メイクアップセット.jpg',
                'categories' => ['コスメ'],
                'condition' => '目立った傷や汚れなし',
            ],
        ];

        foreach ($products as $product) {
            $item = Item::create([
                'user_id' => collect($userIds)->random(),
                'title' => $product['title'],
                'price' => $product['price'],
                'brand_id' => $product['brand'] ? ($brandMap[$product['brand']] ?? null) : null,
                'description' => $product['description'],
                'category_id' => $categoryMap[$product['categories'][0]] ?? Category::inRandomOrder()->first()->id,
                'condition' => $conditionMap[$product['condition']] ?? 1,
                'status' => 'available',
            ]);

            ItemImage::create([
                'item_id' => $item->id,
                'image_path' => 'product_images/' . $product['image'],
            ]);

            $categoryIds = collect($product['categories'])
                ->map(fn($name) => $categoryMap[$name] ?? null)
                ->filter()
                ->unique()
                ->values()
                ->all();

            $item->categories()->attach($categoryIds);
        }
    }
}
