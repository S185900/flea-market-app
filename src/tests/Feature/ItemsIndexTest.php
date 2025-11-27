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
        $user = User::factory()->create();
        $item1 = Item::factory()->create(['user_id' => $user->id]);
        $item2 = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->get('/');
        $response->assertStatus(200);
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
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        Transaction::factory()->create(['item_id' => $item->id]);
        $item->status = 'sold';
        $item->save();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::index
     * 自分が出品した商品は表示されない
     */
    public function it_does_not_display_items_listed_by_logged_in_user()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user);

        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'title' => 'My Own Item'
        ]);

        $otherItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Other User Item'
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('My Own Item');
        $response->assertSee('Other User Item');
    }
}
