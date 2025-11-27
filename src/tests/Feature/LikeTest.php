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
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->get(route('item.detail', $item->id))
            ->assertStatus(200)
            ->assertSee('/images/like-icon-outline.png')
            ->assertDontSee('/images/like-icon-filled.png');

        $this->post(route('item.like', $item))
            ->assertRedirect(route('item.detail', $item));

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertEquals(1, $item->likes()->count());

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
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        $this->get(route('item.detail', $item->id))
            ->assertStatus(200)
            ->assertSee('/images/like-icon-filled.png')
            ->assertDontSee('/images/like-icon-outline.png');

        $this->post(route('item.like', $item))
            ->assertRedirect(route('item.detail', $item));

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertEquals(0, $item->likes()->count());

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
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->get(route('item.detail', $item->id))
            ->assertStatus(200)
            ->assertSee('/images/like-icon-outline.png');

        $this->post(route('item.like', $item))
            ->assertRedirect(route('item.detail', $item));

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

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
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->get(route('item.detail', $item->id))
            ->assertStatus(200)
            ->assertSee('/images/like-icon-outline.png')
            ->assertDontSee('/images/like-icon-filled.png');
    }
}
