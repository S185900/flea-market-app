<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

/**
 * @covers \App\Http\Controllers\LoginUserController
 * @covers \App\Http\Requests\LoginRequest
 */

class LoginTest extends TestCase
{
    // テストごとにDBリセット
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRFだけ無効化
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    // 「メールアドレスを入力してください」というバリデーションメッセージが表示される
    public function test_email_is_required()
    {
        // メールアドレスを入力せずに他の必要項目を入力する場合
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    // 「パスワードを入力してください」というバリデーションメッセージが表示される
    public function test_password_is_required()
    {
        // パスワードを入力せずに他の必要項目を入力する場合
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    // 「ログイン情報が登録されていません」というバリデーションメッセージが表示される
    public function test_login_information_is_not_registered()
    {
        // 必要項目に登録されていない情報を入力する場合
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['auth' => 'ログイン情報が登録されていません']);
    }

    // ログイン処理が実行される
    public function test_successful_login()
    {
        // 全ての必要項目を入力する場合
        $user = User::factory()->create([
            'email' => 'valid@example.com',
            'password' => bcrypt('validpassword'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'valid@example.com',
            'password' => 'validpassword',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

}
