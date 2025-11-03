<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

/**
 * @covers \App\Http\Controllers\LoginUserController
 */

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRFだけ無効化
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    // ログアウト処理が実行される
    public function test_user_can_logout_successfully()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // ユーザーにログインをする
        $this->actingAs($user);

        // ログアウトリクエストを送信（CSRFトークンは自動で付与される）
        $response = $this->post('/logout');

        // 認証状態が解除されていることを確認
        $this->assertGuest();

        // リダイレクト先を確認
        $response->assertRedirect('/');
    }
}
