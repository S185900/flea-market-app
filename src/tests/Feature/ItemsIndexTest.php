<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;

class ItemsIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::index
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
        // すべての商品が表示される
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::index
     */
    public function it_displays_sold_label_for_purchased_items()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        Transaction::factory()->create(['item_id' => $item->id]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sold');
        // 購入済み商品に「Sold」のラベルが表示される
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::index
     */
    public function it_does_not_display_items_listed_by_logged_in_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $otherUser = User::factory()->create();
        $ownItem = Item::factory()->create(['user_id' => $user->id]);
        $otherItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Test Item'
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee($ownItem->title);
        $response->assertSee('Test Item');
        // 自分が出品した商品が一覧に表示されない
    }

}
