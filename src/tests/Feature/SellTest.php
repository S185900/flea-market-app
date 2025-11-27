<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ItemImage;


// 出品商品情報登録のテスト
class SellTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\SellController::showCreateItem
     * @covers \App\Http\Controllers\SellController::storeItem
     * 商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）
     */
    public function test_user_can_create_item_with_image_and_required_fields()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('sell'));
        $response->assertStatus(200);
        $response->assertViewIs('sell_form');

        Storage::fake('public');

        $image = UploadedFile::fake()->image('test_product.jpg');

        $payload = [
            'product_name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト用の商品説明です。',
            'price' => 5000,
            'condition' => 2,
            'categories' => ['家電', 'インテリア'],
            'image' => $image,
        ];

        $response = $this->post(route('sell.store'), $payload);

        $response->assertRedirect(route('mypage.index'));

        $this->assertDatabaseHas('items', [
            'title' => 'テスト商品',
            'description' => 'これはテスト用の商品説明です。',
            'price' => 5000,
            'condition' => 2,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('brands', [
            'brand_name' => 'テストブランド',
        ]);

        foreach ($payload['categories'] as $categoryName) {
            $this->assertDatabaseHas('categories', [
                'category_name' => $categoryName,
            ]);
        }

        $item = Item::where('title', 'テスト商品')->first();
        $this->assertNotNull($item);

        $this->assertDatabaseHas('item_images', [
            'item_id' => $item->id,
        ]);

        $imageRecord = ItemImage::where('item_id', $item->id)->first();
        $this->assertNotNull($imageRecord);

        Storage::disk('public')->assertExists($imageRecord->image_path);
    }
}
