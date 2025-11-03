<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


// 会員登録機能のテスト
class RegisterTest extends TestCase
{

    // テストごとにDBリセット
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\RegisteredUserController::store
     */
    protected function setUp(): void
    {
        parent::setUp();

        // CSRFだけ無効化
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\RegisteredUserController::store
     */
    public function test_name_is_required()
    {
        // 名前を入力せずに他の必要項目を入力する場合
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 「お名前を入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\RegisteredUserController::store
     */
    public function test_email_is_required()
    {
        // メールアドレスを入力せずに他の必要項目を入力する場合
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 「メールアドレスを入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\RegisteredUserController::store
     */
    public function test_password_is_required()
    {
        // パスワードを入力せずに他の必要項目を入力する場合
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password_confirmation' => 'password123',
        ]);

        // 「パスワードを入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\RegisteredUserController::store
     */
    public function test_password_must_be_at_least_8_characters()
    {
        //  7文字以下のパスワードと他の必要項目を入力する場合
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'short07',
            'password_confirmation' => 'short07',
        ]);

        // 「パスワードは8文字以上で入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\RegisteredUserController::store
     */
    public function test_password_and_confirmation_must_match()
    {
        // 確認用パスワードと異なるパスワードを入力し、他の必要項目も入力する場合
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        // 「パスワードと一致しません」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\RegisteredUserController::store
     */
    public function test_successful_registration_redirects_to_profile()
    {
        // 全ての必要項目を正しく入力する場合
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 会員情報が登録され、プロフィール設定画面に遷移する
        $response->assertRedirect(route('mypage.profile'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
}
