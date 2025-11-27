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
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $beforeCount = Comment::where('item_id', $item->id)->count();

        $this->actingAs($user)
            ->post(route('item.comment', $item->id), [
                'comment' => 'This is a test comment.',
            ])
            ->assertRedirect(route('item.detail', $item->id))
            ->assertSessionHas('success', 'コメントを投稿しました');

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'commenter_id' => $user->id,
            'comment' => 'This is a test comment.',
        ]);

        $afterCount = Comment::where('item_id', $item->id)->count();
        $this->assertEquals($beforeCount + 1, $afterCount);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::postComment
     * ログイン前のユーザーはコメントを送信できない
     */
    public function unauthenticated_user_cannot_post_comment()
    {
        $owner = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->post(route('item.comment', $item->id), [
        'comment' => 'Comment from guest',
        ]);

        $response->assertRedirect(route('login'))
                ->assertStatus(302);

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
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('item.comment', $item->id), [
                'comment' => '',
            ]);

        $response->assertSessionHasErrors(['comment']);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ItemController::postComment
     * コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function comment_exceeding_255_characters_should_fail_validation()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $longComment = str_repeat('a', 256);

        $response = $this->actingAs($user)
            ->post(route('item.comment', $item->id), [
                'comment' => $longComment,
            ]);

        $response->assertSessionHasErrors(['comment']);
    }
}

