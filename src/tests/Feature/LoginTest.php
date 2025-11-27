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
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     */
    public function test_email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     */
    public function test_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     */
    public function test_login_information_is_not_registered()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['auth' => 'ログイン情報が登録されていません']);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     */
    public function test_successful_login()
    {
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
