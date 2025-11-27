<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\VerifyEmail as AuthVerifyEmail;
use Illuminate\Support\Facades\URL;
use App\Models\User;


// メール認証機能のテスト
class EmailVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\RegisteredUserController::store
     * 会員登録後、認証メールが送信される
     */
    public function user_can_register_and_receive_verification_email()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('mypage.profile'));

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, AuthVerifyEmail::class);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     * メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
     */
    public function user_can_navigate_to_email_verification_site_from_notice_page()
    {
        Notification::fake();

        app()->detectEnvironment(fn () => 'local');
        config(['app.env' => 'local']);

        $user = User::factory()->unverified()->create();

        $this->actingAs($user);

        $response = $this->get('/email/verify/notice');
        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');

        $content = $response->getContent();
        $this->assertStringContainsString("window.open('http://localhost:8025'", $content);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     * @covers \App\Http\Controllers\RegisteredUserController::store
     * メール認証サイトのメール認証を完了すると、プロフィール認証ページに遷移する
     */
    public function user_can_verify_email_and_redirect_to_profile_creation()
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        $response->assertRedirect(route('mypage.create'));
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ProfileController::storeProfile
     * 初回プロフィール登録完了後、商品一覧ページに遷移する
     */
    public function user_can_complete_profile_and_redirect_to_items_index()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'profile_completed' => false,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('mypage.store'), [
            'name' => 'テストユーザー',
            'postal_code' => '123-4567',
            'shipping_address' => '東京都渋谷区1-2-3',
            'building_name' => 'テストビル',
        ]);

        $response->assertRedirect(route('items.index'));

        $this->assertTrue($user->fresh()->profile_completed);
        $this->assertEquals('テストユーザー', $user->fresh()->name);
    }

}
