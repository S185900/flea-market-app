<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        // 事前に商品とユーザーを作成
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'available']);

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品購入画面を開く（購入確認画面）
        $response = $this->get(route('purchase.form', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertViewIs('purchase_confirm');
        $response->assertViewHas('item', $item);

        // 3. 商品を選択して「購入する」ボタンを押下（購入処理）
        $purchaseResponse = $this->postJson(route('purchase.stripe', ['item' => $item->id]), [
            'payment_method' => 'card',
            'shipping_address' => '123-4567 Tokyo Shibuya 1-2-3',
        ]);
        $purchaseResponse->assertStatus(200);

        // 購入が完了する
        $this->assertDatabaseHas('transactions', [
            'item_id' => $item->id,
            'buyer_id' => $user->id,
            'status' => 'completed',
        ]);

        // 念の為、商品が「sold」になっていることを確認
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
        // 出品者（seller）と購入者（buyer）を別々に作成
        $seller = User::factory()->create(); // 商品を出品するユーザー
        $buyer = User::factory()->create();  // 商品を購入するユーザー

        // 出品者によって商品を作成（status: available）
        $item = Item::factory()->create([
            'status' => 'available',
            'user_id' => $seller->id, // 出品者を明示的に指定
        ]);

        // 1. ユーザーにログインする(購入者としてログイン)
        // $this->actingAs($user);
        $this->actingAs($buyer);

        // 2. 商品購入画面を開く（購入確認画面）
        $this->get(route('purchase.form', ['item_id' => $item->id]))->assertStatus(200);

        // 3. 商品を選択して「購入する」ボタンを押下（購入処理）
        $this->postJson(route('purchase.stripe', ['item' => $item->id]), [
            'payment_method' => 'card',
            'shipping_address' => '123-4567 Tokyo Shibuya 1-2-3',
        ])->assertStatus(200);

        // 4. 商品一覧画面を表示する（この時点で status は 'sold' に更新されている）
        $response = $this->get(route('items.index', ['tab' => 'recommend']));
        $response->assertStatus(200);

        // dd($response->viewData('items'));

        // ビューに渡された items に、購入済みの商品が含まれていることを確認
        $response->assertViewHas('items', function ($items) use ($item) {
            $target = collect($items)->firstWhere('id', $item->id);
            return $target && $target->status === 'sold';
        });

        // 購入した商品が「Sold」として表示されている
        // HTMLに「Sold」などの表示が含まれていることを確認（ビューに応じて調整）
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
        // 事前に商品とユーザーを作成
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'available']);

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品購入画面を開く（購入確認画面）
        $response = $this->get(route('purchase.form', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertViewIs('purchase_confirm');
        $response->assertViewHas('item', $item);

        // 3. 商品を選択して「購入する」ボタンを押下（購入処理）
        $this->postJson(route('purchase.stripe', ['item' => $item->id]), [
            'payment_method' => 'card',
            'shipping_address' => '123-4567 Tokyo Shibuya 1-2-3',
        ])->assertStatus(200);

        // 4. プロフィール画面を表示する
        $profileResponse = $this->get(route('mypage.index'));
        $profileResponse->assertStatus(200);

        // 購入した商品がプロフィールの購入した商品一覧に追加されている
        $profileResponse->assertSee($item->title);

    }

}
