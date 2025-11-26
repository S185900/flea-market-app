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

        // 登録されたユーザーを取得
        $user = User::where('email', 'test@example.com')->first();

        // 2. 認証メールを送信する
        Notification::assertSentTo($user, AuthVerifyEmail::class);
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

        // 環境を一時的に local に偽装
        app()->detectEnvironment(fn () => 'local');
        config(['app.env' => 'local']);

        // 未認証ユーザーを作成
        $user = User::factory()->unverified()->create();

        // ログイン状態にする
        $this->actingAs($user);

        // 1. メール認証導線画面を表示する
        $response = $this->get('/email/verify/notice');
        $response->assertStatus(200);

        // 2. 「認証はこちらから」ボタンを押下
        $response->assertSee('認証はこちらから');

        // 3. メール認証サイトを表示する
        // エスケープの影響を避けて確認
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

        // 2. プロフィール設定画面を表示する
        // 認証完了後はプロフィール設定画面に遷移する
        $response->assertRedirect(route('mypage.create'));
    }

    /**
     * @test
     * @covers \App\Http\Controllers\ProfileController::storeProfile
     * 初回プロフィール登録完了後、商品一覧ページに遷移する
     */
    public function user_can_complete_profile_and_redirect_to_items_index()
    {
        // 未認証ユーザーを作成してメール認証済みにする
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'profile_completed' => false, // まだプロフィール登録していない＝初回登録前の状態
        ]);

        // ログイン状態にする
        $this->actingAs($user);

        // 初回プロフィール登録を実行
        $response = $this->post(route('mypage.store'), [
            'name' => 'テストユーザー',
            'postal_code' => '123-4567',
            'shipping_address' => '東京都渋谷区1-2-3',
            'building_name' => 'テストビル',
            // 画像は省略（ファイルアップロードのテストは別途）
        ]);

        // プロフィール登録が完了し、商品一覧ページにリダイレクトされる
        $response->assertRedirect(route('items.index'));

        // ユーザー情報が更新されていることを確認
        $this->assertTrue($user->fresh()->profile_completed);
        $this->assertEquals('テストユーザー', $user->fresh()->name);
    }

}
