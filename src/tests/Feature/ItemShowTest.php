<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\ItemImage;


// 商品詳細情報取得のテスト
class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::showItemDetail
     * 必要な情報が表示される、複数選択されたカテゴリが表示されているか
     */
    public function test_item_detail_page_displays_required_information()
    {
        // 事前に商品と関連情報を作成

        Storage::fake('public');

        $user = User::factory()->create();
        $brand = Brand::factory()->create(['brand_name' => 'TestBrand']);
        $categories = Category::factory()->count(2)->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'brand_id' => $brand->id,
            'title' => 'Test Item',
            'price' => 12345,
            'description' => 'This is a test item.',
            'condition' => 1, // 新品
        ]);

        $item->categories()->attach($categories->pluck('id'));

        $imagePath = 'items/test-image.jpg';
        ItemImage::factory()->create([
            'item_id' => $item->id,
            'image_path' => $imagePath,
        ]);

        // 異なるユーザーで「いいね」を3件作る
        for ($i = 0; $i < 3; $i++) {
            $liker = User::factory()->create();
            Like::factory()->create([
                'item_id' => $item->id,
                'user_id' => $liker->id,
            ]);
        }

        Comment::factory()->count(2)->create([
            'item_id' => $item->id,
            'commenter_id' => $user->id,
            'comment' => 'Nice item!',
        ]);

        // 1. 商品詳細ページを開く
        $response = $this->get(route('item.detail', $item->id));
        $response->assertStatus(200);

        // 以下、すべての情報が商品詳細ページに表示されている

        // 商品画像
        $expectedImageUrl = asset('storage/' . $imagePath);
        $response->assertSee($expectedImageUrl);

        // 商品名
        $response->assertSee($item->title);

        // ブランド名
        $response->assertSee($brand->name);

        // 価格
        $response->assertSee(number_format($item->price));

        // いいね数・コメント数
        $response->assertSee('3'); // いいね数
        $response->assertSee('2'); // コメント数

        // 商品説明
        $response->assertSee('This is a test item.');

        // 商品情報（カテゴリ、商品の状態）
        // 複数選択されたカテゴリが商品詳細ページに表示されている
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
        $response->assertSee('良好'); // condition = 1 の表示名

        // コメントしたユーザー情報
        $response->assertSee($user->name);

        // コメント内容
        $response->assertSee('Nice item!');

    }
}
