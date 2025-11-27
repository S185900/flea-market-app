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
            'condition' => 1,
        ]);

        $item->categories()->attach($categories->pluck('id'));

        $imagePath = 'items/test-image.jpg';
        ItemImage::factory()->create([
            'item_id' => $item->id,
            'image_path' => $imagePath,
        ]);

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

        $response = $this->get(route('item.detail', $item->id));
        $response->assertStatus(200);
        $expectedImageUrl = asset('storage/' . $imagePath);
        $response->assertSee($expectedImageUrl);
        $response->assertSee($item->title);
        $response->assertSee($brand->name);
        $response->assertSee(number_format($item->price));
        $response->assertSee('3'); // いいね数
        $response->assertSee('2'); // コメント数
        $response->assertSee('This is a test item.');

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
        $response->assertSee('良好');
        $response->assertSee($user->name);
        $response->assertSee('Nice item!');
    }
}
