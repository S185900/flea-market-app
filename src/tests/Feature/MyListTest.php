<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Transaction;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::index
     * いいねをした商品が表示される
     */
    public function liked_items_are_displayed_in_mylist_for_authenticated_user()
    {
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

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('テスト商品A');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::index
     */
    public function sold_label_is_displayed_for_purchased_items_in_mylist()
    {
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

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('sold');
        // 購入済み商品に「sold」のラベルが表示される
    }

    /** @test
     * @covers \App\Http\Controllers\ItemController::index
     */
    public function nothing_is_displayed_for_guest_user_in_mylist_tab()
    {
        // 事前に商品を作成しておく（おすすめタブに表示される可能性を排除）
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => 'ゲスト用商品',
        ]);

        // 未認証状態で mylist タブにアクセス
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);

        // 何も表示されない
        $response->assertDontSee('ゲスト用商品');
        $response->assertDontSee('sold');

    }

}
