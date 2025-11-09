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
        // 事前にユーザーを作成
        $user = User::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品出品画面を開く
        $response = $this->get(route('sell'));
        $response->assertStatus(200);
        $response->assertViewIs('sell_form');

        // 以下、3. 各項目に適切な情報を入力して保存する

        // ストレージをフェイク
        Storage::fake('public');

        // 画像を選択
        $image = UploadedFile::fake()->image('test_product.jpg');

        // 各項目を入力
        $payload = [
            'product_name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト用の商品説明です。',
            'price' => 5000,
            'condition' => 2,
            'categories' => ['家電', 'インテリア'],
            'image' => $image,
        ];

        // 出品する
        $response = $this->post(route('sell.store'), $payload);

        // リダイレクト確認
        $response->assertRedirect(route('mypage.index'));

        // 各項目が正しく保存されている
        $this->assertDatabaseHas('items', [
            'title' => 'テスト商品',
            'description' => 'これはテスト用の商品説明です。',
            'price' => 5000,
            'condition' => 2,
            'user_id' => $user->id,
        ]);

        // ブランドが保存されているか確認
        $this->assertDatabaseHas('brands', [
            'brand_name' => 'テストブランド',
        ]);

        // カテゴリーが保存されているか確認
        foreach ($payload['categories'] as $categoryName) {
            $this->assertDatabaseHas('categories', [
                'category_name' => $categoryName,
            ]);
        }

        // 商品画像が保存されているか確認
        $item = Item::where('title', 'テスト商品')->first();
        $this->assertNotNull($item);

        $this->assertDatabaseHas('item_images', [
            'item_id' => $item->id,
        ]);

        $imageRecord = ItemImage::where('item_id', $item->id)->first();
        $this->assertNotNull($imageRecord);

        // ファイル保存の確認
        Storage::disk('public')->assertExists($imageRecord->image_path);
    }
}
