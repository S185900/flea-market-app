<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

// コメント送信機能のテスト
class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::postComment
     * ログイン済みのユーザーはコメントを送信できる
     */
    public function authenticated_user_can_post_comment()
    {
        // 事前にユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 投稿前のコメント数を記録
        $beforeCount = Comment::where('item_id', $item->id)->count();

        // 1. ユーザーにログインする、2. コメントを入力する、3. コメントボタンを押す
        $this->actingAs($user)
            ->post(route('item.comment', $item->id), [
                'comment' => 'This is a test comment.',
            ])
            ->assertRedirect(route('item.detail', $item->id))
            ->assertSessionHas('success', 'コメントを投稿しました');

        // コメントが保存されていることを確認
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'commenter_id' => $user->id,
            'comment' => 'This is a test comment.',
        ]);

        // 投稿後のコメント数を取得
        $afterCount = Comment::where('item_id', $item->id)->count();
        // コメント数が1増えていることを確認
        $this->assertEquals($beforeCount + 1, $afterCount);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::postComment
     * ログイン前のユーザーはコメントを送信できない
     */
    public function unauthenticated_user_cannot_post_comment()
    {
        // 商品の出品者を作成
        $owner = User::factory()->create();

        // 商品を作成（出品者を紐付ける）
        $item = Item::factory()->create([
            'user_id' => $owner->id,
        ]);

        // コメント送信実行
        $response = $this->post(route('item.comment', $item->id), [
        'comment' => 'Comment from guest',
        ]);

        // 送信されていないことを確認（リダイレクトされる）
        $response->assertRedirect(route('login'))
                ->assertStatus(302);

        // 念の為、データベースにコメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'comment' => 'Comment from guest',
        ]);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::postComment
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function empty_comment_should_fail_validation()
    {
        // 事前にユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 1. ユーザーにログインする、コメントを空欄状態にする、2. コメントボタンを押す
        $response = $this->actingAs($user)
            ->post(route('item.comment', $item->id), [
                'comment' => '',
            ]);

        // バリデーションメッセージが表示される
        $response->assertSessionHasErrors(['comment']);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::postComment
     * コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function comment_exceeding_255_characters_should_fail_validation()
    {
        // 事前にユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 256字のコメントを作成
        $longComment = str_repeat('a', 256);

        // 1. ユーザーにログインする、2. 255文字以上のコメントを入力する、3. コメントボタンを押す
        $response = $this->actingAs($user)
            ->post(route('item.comment', $item->id), [
                'comment' => $longComment,
            ]);

        // バリデーションメッセージが表示される
        $response->assertSessionHasErrors(['comment']);
    }
}

