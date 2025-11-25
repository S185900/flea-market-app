<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;


// ログイン機能のテスト
class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     */
    protected function setUp(): void
    {
        parent::setUp();

        // CSRFだけ無効化
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     */
    public function test_email_is_required()
    {
        // メールアドレスを入力せずに他の必要項目を入力する場合
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'secret123',
        ]);

        // 「メールアドレスを入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     */
    public function test_password_is_required()
    {
        // パスワードを入力せずに他の必要項目を入力する場合
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        // 「パスワードを入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     */
    public function test_login_information_is_not_registered()
    {
        // 必要項目に登録されていない情報を入力する場合
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        // 「ログイン情報が登録されていません」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors(['auth' => 'ログイン情報が登録されていません']);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     */
    public function test_successful_login()
    {
        // 全ての必要項目を入力する場合
        $user = User::factory()->create([
            'email' => 'valid@example.com',
            'password' => bcrypt('validpassword'),
            'email_verified_at' => now(),
        ]);

        // ログイン処理が実行される
        $response = $this->post('/login', [
            'email' => 'valid@example.com',
            'password' => 'validpassword',
        ]);

        // ホーム画面にリダイレクトされ、認証状態になっていることを確認
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

}
