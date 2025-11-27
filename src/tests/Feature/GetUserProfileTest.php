<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\ItemImage;


// ユーザー情報取得のテスト
class GetUserProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\ProfileController::showProfileIndex
     * 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     * sellタブでは出品商品のみが表示され、購入商品は表示されない
     */
    public function test_user_can_view_only_listed_items_on_sell_tab()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image_url' => 'storage/profile_images/fake-profile.jpg',
        ]);

        $this->actingAs($user);

        $listedItem = Item::factory()->create([
            'user_id' => $user->id,
            'title' => '出品商品A',
        ]);

        ItemImage::factory()->create([
            'item_id' => $listedItem->id,
            'image_path' => 'items/fake-listed.jpg',
        ]);

        $purchasedItem = Item::factory()->create([
            'title' => '購入商品B',
        ]);

        ItemImage::factory()->create([
            'item_id' => $purchasedItem->id,
            'image_path' => 'items/fake-purchased.jpg',
        ]);

        Transaction::factory()->create([
            'item_id' => $purchasedItem->id,
            'buyer_id' => $user->id,
            'seller_id' => $purchasedItem->user_id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->get(route('mypage.index', ['page' => 'sell']));
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('storage/profile_images/fake-profile.jpg');

        $html = $response->getContent();
        preg_match('/<div id="listed-items" class="items-index">\s*<div class="items-grid">(.*?)<\/div>/s', $html, $matches);
        $itemsGridHtml = $matches[1] ?? '';

        $this->assertStringContainsString('出品商品A', $itemsGridHtml);
        $this->assertStringContainsString('items/fake-listed.jpg', $itemsGridHtml);

        $this->assertStringNotContainsString('購入商品B', $itemsGridHtml);
        $this->assertStringNotContainsString('items/fake-purchased.jpg', $itemsGridHtml);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ProfileController::showProfileIndex
     * 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     * buyタブでは購入商品のみが表示され、出品商品は表示されない
     */
    public function test_user_can_view_only_purchased_items_on_buy_tab()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image_url' => 'storage/profile_images/fake-profile.jpg',
        ]);

        $this->actingAs($user);

        $listedItem = Item::factory()->create([
            'user_id' => $user->id,
            'title' => '出品商品A',
        ]);

        ItemImage::factory()->create([
            'item_id' => $listedItem->id,
            'image_path' => 'items/fake-listed.jpg',
        ]);

        $otherUser = User::factory()->create();

        $purchasedItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'title' => '購入商品B',
        ]);

        ItemImage::factory()->create([
            'item_id' => $purchasedItem->id,
            'image_path' => 'items/fake-purchased.jpg',
        ]);

        Transaction::factory()->create([
            'item_id' => $purchasedItem->id,
            'buyer_id' => $user->id,
            'seller_id' => $purchasedItem->user_id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->get(route('mypage.index', ['page' => 'buy']));
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('storage/profile_images/fake-profile.jpg');
        $response->assertSee('購入商品B');
        $response->assertSee('items/fake-purchased.jpg');
        $response->assertDontSee('出品商品A');
        $response->assertDontSee('items/fake-listed.jpg');
    }
}
