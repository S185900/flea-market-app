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
        // 事前にユーザーと商品を作成
        $this->user = User::factory()->create();
        $this->item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($this->user);

        // 2. 送付先住所変更画面で住所を登録する
        $response = $this->post(route('address.update', ['item_id' => $this->item->id]), [
            'postal_code' => '123-4567',
            'address' => '岡山県倉敷市',
            'building_name' => 'テストビル101',
        ]);

        // リダイレクト
        $response->assertRedirect(route('purchase.form', ['item_id' => $this->item->id]));
        $response->assertSessionHas('message', '住所を更新しました');

        // 3. 商品購入画面を再度開く
        $purchaseResponse = $this->get(route('purchase.form', ['item_id' => $this->item->id]));
        $purchaseResponse->assertStatus(200);

        // 登録した住所が商品購入画面に正しく反映される
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
        // 事前にユーザーと商品を作成
        $this->user = User::factory()->create();
        $this->item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($this->user);

        // 2. 送付先住所変更画面で住所を登録する 
        $this->post(route('address.update', ['item_id' => $this->item->id]), [
            'postal_code' => '987-6543',
            'address' => '東京都渋谷区',
            'building_name' => 'テストマンション202',
        ]);

        // 3. 商品を購入する（仮の購入処理ルート）
        $purchaseResponse = $this->post(route('purchase.confirm', ['item' => $this->item->id]));

        // 成功後のリダイレクト想定
        $purchaseResponse->assertStatus(200);

        // 正しく送付先住所が紐づいている
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'postal_code' => '987-6543',
            'shipping_address' => '東京都渋谷区',
            'building_name' => 'テストマンション202',
        ]);
    }
}
