<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;

// いいね機能のテスト
class LikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::postLike
     * いいねアイコンを押下することによって、いいねした商品として登録することができる
     */
    public function user_can_like_an_item()
    {
        // 事前にユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品詳細ページを開く（未いいね状態）
        $this->get(route('item.detail', $item->id))
            ->assertStatus(200)
            ->assertSee('/images/like-icon-outline.png')
            ->assertDontSee('/images/like-icon-filled.png');

        // 3. いいねアイコンを押下（POSTリクエスト）
        $this->post(route('item.like', $item))
            ->assertRedirect(route('item.detail', $item));

        // DBにいいねが登録されていることを確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 商品のいいね数が1であることを確認(いいねした商品として登録され、いいね合計値が増加表示される)
        $this->assertEquals(1, $item->likes()->count());

        // いいね済み状態で再度ページを表示
        $this->get(route('item.detail', $item->id))
            ->assertStatus(200)
            ->assertSee('/images/like-icon-filled.png')
            ->assertDontSee('/images/like-icon-outline.png');
    }

    /** @test
     * @covers \App\Http\Controllers\ItemController::postLike
     * 再度いいねアイコンを押下することによって、いいねを解除することができる
     */
    public function user_can_unlike_an_item()
    {
        // 事前にユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // いいね済みの状態を作成
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品詳細ページを開く（いいね済み状態）
        $this->get(route('item.detail', $item->id))
            ->assertStatus(200)
            ->assertSee('/images/like-icon-filled.png')
            ->assertDontSee('/images/like-icon-outline.png');

        // 3. いいねアイコンを押下（解除のPOSTリクエスト）
        $this->post(route('item.like', $item))
            ->assertRedirect(route('item.detail', $item));

        // DBからいいねが削除されていることを確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 商品のいいね数が0であることを確認(いいねが解除され、いいね合計値が減少表示される)
        $this->assertEquals(0, $item->likes()->count());

        // 再度ページを表示して、アイコンがoutlineに戻っていることを確認
        $this->get(route('item.detail', $item->id))
            ->assertStatus(200)
            ->assertSee('/images/like-icon-outline.png')
            ->assertDontSee('/images/like-icon-filled.png');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::showItemDetail
     * 追加済みのアイコンは色が変化する
     * いいねアイコンが押下された状態では色が変化する(色がつく場合)
     */
    public function liked_item_displays_filled_like_icon()
    {
        // 事前にユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品詳細ページを開く
        $this->get(route('item.detail', $item->id))
            ->assertStatus(200)
            ->assertSee('/images/like-icon-outline.png'); // 未いいね状態

        // 3. いいねアイコンを押下（POSTリクエスト）
        $this->post(route('item.like', $item))
            ->assertRedirect(route('item.detail', $item));

        // いいねが登録されていることを確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね済み状態で再度ページを表示
        // いいね済み → like-icon-filled.png が表示される
        $this->get(route('item.detail', $item->id))
            ->assertStatus(200)
            ->assertSee('/images/like-icon-filled.png');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::showItemDetail
     * 追加済みのアイコンは色が変化する
     * いいねアイコンが押下された状態では色が変化する(色が消える場合)
     */
    public function unliked_item_displays_outline_like_icon()
    {
        // 事前にユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品詳細ページを開く（いいねしていない状態）
        // 未いいね → like-icon-outline.png が表示される
        $this->get(route('item.detail', $item->id))
            ->assertStatus(200)
            ->assertSee('/images/like-icon-outline.png')
            ->assertDontSee('/images/like-icon-filled.png');
    }

}
