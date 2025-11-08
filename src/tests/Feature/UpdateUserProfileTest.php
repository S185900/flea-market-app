<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;


// ユーザー情報変更のテスト
class UpdateUserProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @covers \App\Http\Controllers\ProfileController::showEditProfile
     * 変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）
     */
    public function profile_edit_page_displays_initial_values_correctly()
    {
        // 事前にテスト用の画像を保存
        Storage::fake('public'); // ストレージをフェイク
        $fakeImagePath = 'profile_images/test.jpg';
        Storage::disk('public')->put($fakeImagePath, 'dummy content');

        // 事前にユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'postal_code' => '123-4567',
            'shipping_address' => '岡山県倉敷市',
            'building_name' => 'テストビル101',
            'profile_image_url' => '/storage/' . $fakeImagePath, // Blade側と一致させる
        ]);

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. プロフィールページを開く
        $response = $this->get(route('mypage.profile'));
        $response->assertStatus(200);

        // 各項目の初期値が正しく表示されている
        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('岡山県倉敷市');
        $response->assertSee('テストビル101');

        // 画像URLが含まれているか確認（imgタグのsrc属性）
        $response->assertSee('/storage/' . $fakeImagePath);
    }
}
