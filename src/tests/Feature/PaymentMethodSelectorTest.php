<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;


// 支払い方法選択機能のテスト
class PaymentMethodSelectorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\PurchaseController::confirm
     * 小計画面で変更が反映される
     */
    public function user_can_select_payment_method_and_see_it_on_purchase_confirm_screen()
    {
        // 事前にユーザーと商品を作成
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'shipping_address' => '岡山県倉敷市',
            'building_name' => 'ビルディング101',
        ]);
        $item = Item::factory()->create();

        // ユーザーにログイン後、1. 支払い方法選択画面を開く
        // 2. プルダウンメニューから支払い方法を選択する
        $response = $this->actingAs($user)->post(route('purchase.confirm', $item), [
            'payment_method' => 'card',
        ]);

        $response->assertStatus(200);

        // 選択した支払い方法が正しく反映される
        $response->assertViewIs('purchase_confirm');
        $response->assertViewHas('selectedMethod', 'card');
        $response->assertSee('カード支払い'); // ビューに表示されているか確認
        $response->assertSee('<option value="card" selected>', false); // HTMLタグをそのまま確認
    }
}
