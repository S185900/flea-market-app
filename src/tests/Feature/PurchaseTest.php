<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;


// 商品購入機能のテスト(テスト環境ではStripe APIをスキップ)
class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\PurchaseController::showPurchaseForm
     * @covers \App\Http\Controllers\PurchaseController::redirectToStripe
     * 「購入する」ボタンを押下すると購入が完了する(トランザクションが記録される)
     */
    public function test_user_can_complete_purchase()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'available']);

        $this->actingAs($user);

        $response = $this->get(route('purchase.form', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertViewIs('purchase_confirm');
        $response->assertViewHas('item', $item);

        $purchaseResponse = $this->postJson(route('purchase.stripe', ['item' => $item->id]), [
            'payment_method' => 'card',
            'shipping_address' => '123-4567 Tokyo Shibuya 1-2-3',
        ]);
        $purchaseResponse->assertStatus(200);

        $this->assertDatabaseHas('transactions', [
            'item_id' => $item->id,
            'buyer_id' => $user->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\PurchaseController::showPurchaseForm
     * @covers \App\Http\Controllers\PurchaseController::redirectToStripe
     * 購入した商品は商品一覧画面にて「sold」と表示される
     */
    public function test_purchased_item_is_marked_as_sold()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create([
            'status' => 'available',
            'user_id' => $seller->id,
        ]);

        $this->actingAs($buyer);

        $this->get(route('purchase.form', ['item_id' => $item->id]))->assertStatus(200);

        $this->postJson(route('purchase.stripe', ['item' => $item->id]), [
            'payment_method' => 'card',
            'shipping_address' => '123-4567 Tokyo Shibuya 1-2-3',
        ])->assertStatus(200);

        $response = $this->get(route('items.index', ['tab' => 'recommend']));
        $response->assertStatus(200);

        $response->assertViewHas('items', function ($items) use ($item) {
            $target = collect($items)->firstWhere('id', $item->id);
            return $target && $target->status === 'sold';
        });

        $response->assertSee('Sold');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\PurchaseController::showPurchaseForm
     * @covers \App\Http\Controllers\PurchaseController::redirectToStripe
     * 「プロフィール/購入した商品一覧」に追加されている
     */
    public function test_purchased_item_appears_in_user_profile()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'available']);

        $this->actingAs($user);

        $response = $this->get(route('purchase.form', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertViewIs('purchase_confirm');
        $response->assertViewHas('item', $item);

        $this->postJson(route('purchase.stripe', ['item' => $item->id]), [
            'payment_method' => 'card',
            'shipping_address' => '123-4567 Tokyo Shibuya 1-2-3',
        ])->assertStatus(200);

        $profileResponse = $this->get(route('mypage.index'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee($item->title);
    }
}
