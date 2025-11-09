<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
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
        // 通知をフェイク
        Notification::fake();

        // 1. 会員登録をする
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('mypage.profile'));

        $user = User::where('email', 'test@example.com')->first();

        // 2. 認証メールを送信する（未認証ユーザーが誘導画面にアクセス）
        $this->actingAs($user)->get(route('verification.notice.notice'));

        // 登録したメールアドレス宛に認証メールが送信されている
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     * メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
     */
    public function user_can_navigate_to_email_verification_site_from_notice_page()
    {
        // 通知をフェイク
        Notification::fake();

        // 未認証ユーザーを作成
        $user = User::factory()->unverified()->create();

        // ログイン状態にする
        $this->actingAs($user);

        // 1. メール認証導線画面を表示する
        $response = $this->get(route('verification.notice.notice'));
        $response->assertStatus(200);

        // 2. 「認証はこちらから」ボタンを押下
        $response->assertSee('認証はこちらから');

        // 3. メール認証サイトを表示する(メール認証サイトに遷移する)
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\LoginUserController::store
     * @covers \App\Http\Controllers\RegisteredUserController::store
     * メール認証サイトのメール認証を完了すると、プロフィール認証ページに遷移する
     */
    public function user_can_verify_email_and_redirect_to_profile_creation()
    {
        // 未認証ユーザーを作成
        $user = User::factory()->unverified()->create();

        // メール認証リンクを生成（Fortifyの署名付きURL）
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // ログイン状態で認証リンクにアクセス
        $response = $this->actingAs($user)->get($verificationUrl);

        // 1. メール認証を完了する
        // Fortifyのルート `/email/verify/{id}/{hash}` にアクセスすることで
        // `EmailVerificationRequest::fulfill()` が呼ばれ、メール認証が完了する
        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        // 2. プロフィール設定画面に遷移する
        // 認証完了後は `mypage.create` にリダイレクトされる仕様
        $response->assertRedirect(route('mypage.create'));
    }
}
