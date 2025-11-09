<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Transaction;


// マイリスト一覧取得のテスト
class MyListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::index
     * いいねした商品だけが表示される
     */
    public function liked_items_are_displayed_in_mylist_for_authenticated_user()
    {
        // 事前に商品を作成し、いいねをする
        $user = User::factory()->create(); // ログインユーザー
        $seller = User::factory()->create(); // 出品者
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => 'テスト商品A',
        ]);
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 1. ユーザーにログインをする、2. マイリストページを開く
        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);

        // いいねをした商品が表示される
        $response->assertSee('テスト商品A');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::index
     * 購入済み商品は「Sold」と表示される
     */
    public function sold_label_is_displayed_for_purchased_items_in_mylist()
    {
        // 事前に商品を作成し、購入状態にする
        $seller = User::factory()->create();
        $user = User::factory()->create(); // 購入者
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => '購入済み商品',
        ]);
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        Transaction::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $user->id,
        ]);

        // 1. ユーザーにログインをする、2. マイリストページを開く
        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);

        // 3. 購入済み商品を確認する(購入済み商品に「Sold」のラベルが表示される)
        $response->assertSee('sold');
    }

    /** @test
     * @covers \App\Http\Controllers\ItemController::index
     * 未認証の場合は何も表示されない
     */
    public function nothing_is_displayed_for_guest_user_in_mylist_tab()
    {
        // 事前に商品を作成
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => 'ゲスト用商品',
        ]);

        // (未認証状態で) 1. マイリストページを開く
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);

        // (未認証の場合は) 何も表示されない
        $response->assertDontSee('ゲスト用商品');
        $response->assertDontSee('sold');

    }

}
