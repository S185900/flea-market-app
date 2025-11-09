<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;


// 商品検索機能のテスト
class HeaderSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::search
     * 「商品名」で部分一致検索ができる
     */
    public function test_partial_match_search_displays_correct_items()
    {
        $user = \App\Models\User::factory()->create();

        Item::factory()->create([
            'title' => '赤い帽子',
            'user_id' => $user->id,
        ]);

        Item::factory()->create([
            'title' => '青いシャツ',
            'user_id' => $user->id,
        ]);

        Item::factory()->create([
            'title' => '赤い靴',
            'user_id' => $user->id,
        ]);

        // 1. 検索欄にキーワードを入力、2. 検索ボタンを押す
        $response = $this->get('/?title=赤');

        // 部分一致する商品が表示される
        $response->assertStatus(200); // 成功
        $response->assertSee('赤い帽子');
        $response->assertSee('赤い靴');
        $response->assertDontSee('青いシャツ');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::search
     * 検索状態がマイリストでも保持されている
     */
    public function test_search_keyword_is_retained_when_switching_to_mylist()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // ログインユーザー以外が出品した商品・それが「いいね」されている状態を作成
        $item = Item::factory()->create([
            'title' => '赤い帽子',
            'user_id' => $otherUser->id,
        ]);
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // ログイン状態にする
        $this->actingAs($user);

        // 1. ホームページで商品を検索、2. 検索結果が表示される
        $homeResponse = $this->get('/?title=赤');
        $homeResponse->assertStatus(200);
        $homeResponse->assertViewHas('title', '赤');

        // 3. マイリストページに遷移（検索キーワードが保持されている）
        $mylistResponse = $this->get('/?tab=mylist&title=赤');
        $mylistResponse->assertStatus(200);
        $mylistResponse->assertSee('赤い帽子');
        $mylistResponse->assertViewHas('title', '赤');
        $mylistResponse->assertViewHas('tab', 'mylist');
    }
}
