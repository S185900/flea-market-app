<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;


// 配送先変更機能のテスト
class AddressEditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\AddressController::showEditAddress
     * @covers \App\Http\Controllers\AddressController::updateAddress
     * 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
     */
    public function address_is_reflected_on_purchase_screen()
    {
        $this->user = User::factory()->create();
        $this->item = Item::factory()->create();
        $this->actingAs($this->user);

        $response = $this->post(route('address.update', ['item_id' => $this->item->id]), [
            'postal_code' => '123-4567',
            'address' => '岡山県倉敷市',
            'building_name' => 'テストビル101',
        ]);

        $response->assertRedirect(route('purchase.form', ['item_id' => $this->item->id]));
        $response->assertSessionHas('message', '住所を更新しました');

        $purchaseResponse = $this->get(route('purchase.form', ['item_id' => $this->item->id]));
        $purchaseResponse->assertStatus(200);
        $purchaseResponse->assertSee('123-4567');
        $purchaseResponse->assertSee('岡山県倉敷市');
        $purchaseResponse->assertSee('テストビル101');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\AddressController::showEditAddress
     * @covers \App\Http\Controllers\AddressController::updateAddress
     * 購入した商品に送付先住所が紐づいて登録される
     */
    public function purchased_item_is_linked_to_updated_address()
    {
        $this->user = User::factory()->create();
        $this->item = Item::factory()->create();
        $this->actingAs($this->user);

        $this->post(route('address.update', ['item_id' => $this->item->id]), [
            'postal_code' => '987-6543',
            'address' => '東京都渋谷区',
            'building_name' => 'テストマンション202',
        ]);

        $purchaseResponse = $this->post(route('purchase.confirm', ['item' => $this->item->id]));
        $purchaseResponse->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'postal_code' => '987-6543',
            'shipping_address' => '東京都渋谷区',
            'building_name' => 'テストマンション202',
        ]);
    }
}
