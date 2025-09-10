<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\LoginUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SellController;

Route::get('/register', fn () => view('auth.register'))->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', [LoginUserController::class, 'store']);

Route::get('/email/verified', function () {
    return view('auth.verify-email');
});

Route::middleware(['auth', 'verified', 'first.login'])->group(function () {
    Route::get('/', fn () => view('items_index'))->name('items.index');

    Route::get('/mypage/profile', [ProfileController::class, 'showEditProfile'])->name('mypage.profile');
    Route::get('/sell', [SellController::class, 'showCreateItem'])->name('sell');

    // 出品フォームの送信もログイン必須になる
    // Route::post('/sell', [SellController::class, 'storeItem']);
});

Route::get('/mypage/profile', [ProfileController::class, 'showEditProfile'])->name('mypage.profile');

Route::get('/', [ItemController::class, 'index'])->name('items.index');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




