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
        $user = User::factory()->create();
        $seller = User::factory()->create();

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
     * 購入済み商品は「Sold」と表示される
     */
    public function sold_label_is_displayed_for_purchased_items_in_mylist()
    {
        $seller = User::factory()->create();
        $user = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => '購入済み商品',
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $item->status = 'sold';

        $item->save();
        Transaction::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /** @test
     * @covers \App\Http\Controllers\ItemController::index
     * 未認証の場合は何も表示されない
     */
    public function nothing_is_displayed_for_guest_user_in_mylist_tab()
    {
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => 'ゲスト用商品',
        ]);

        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertDontSee('ゲスト用商品');
        $response->assertDontSee('sold');
    }
}
