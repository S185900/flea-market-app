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
        Storage::fake('public');
        $fakeImagePath = 'profile_images/test.jpg';
        Storage::disk('public')->put($fakeImagePath, 'dummy content');

        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'postal_code' => '123-4567',
            'shipping_address' => '岡山県倉敷市',
            'building_name' => 'テストビル101',
            'profile_image_url' => '/storage/' . $fakeImagePath,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage.profile'));
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('岡山県倉敷市');
        $response->assertSee('テストビル101');
        $response->assertSee('/storage/' . $fakeImagePath);
    }
}
