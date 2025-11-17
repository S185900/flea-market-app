<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ランダムユーザー10人を作成
        User::factory()->count(10)->create();

        // 管理者ユーザー
        User::create([
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 一般ユーザー
        User::create([
            'name' => '一般ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
