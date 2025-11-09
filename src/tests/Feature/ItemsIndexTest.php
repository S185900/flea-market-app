<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;


// 商品一覧取得のテスト
class ItemsIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::index
     * 全商品を取得できる
     */
    public function it_displays_all_items()
    {
        // 事前にユーザーと商品を作成
        $user = User::factory()->create();
        $item1 = Item::factory()->create(['user_id' => $user->id]);
        $item2 = Item::factory()->create(['user_id' => $user->id]);

        // 1. 商品ページを開く
        $response = $this->get('/');
        $response->assertStatus(200);

        // すべての商品が表示される
        $response->assertSee($item1->title);
        $response->assertSee($item2->title);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::index
     * 購入済み商品は「Sold」と表示される
     */
    public function it_displays_sold_label_for_purchased_items()
    {
        // 事前に購入済みの商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        Transaction::factory()->create(['item_id' => $item->id]);

        // 1. 商品ページを開く、2. 購入済み商品を表示する
        $response = $this->get('/');
        $response->assertStatus(200);

        // 購入済み商品に「Sold」のラベルが表示される
        $response->assertSee('sold');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::index
     * 自分が出品した商品は表示されない
     */
    public function it_does_not_display_items_listed_by_logged_in_user()
    {
        // 事前にユーザーを作成
        $user = User::factory()->create();

        // 1. ユーザーにログインをする
        $this->actingAs($user);

        // 事前に自分が出品した商品と他のユーザーが出品した商品を作成
        $otherUser = User::factory()->create();
        $ownItem = Item::factory()->create(['user_id' => $user->id]);
        $otherItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Test Item'
        ]);

        // 2. 商品ページを開く
        $response = $this->get('/');
        $response->assertStatus(200);

        // 自分が出品した商品が一覧に表示されない
        $response->assertDontSee($ownItem->title);
        $response->assertSee('Test Item');
    }

}
