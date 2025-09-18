<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\LoginUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SellController;

// 未認証でもアクセス可能な商品一覧ページ(おすすめタブがデフォルトで表示される)
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細ページ
Route::get('/item/{item_id}', [ItemController::class, 'showItemDetail'])->name('item.detail');

// コメント投稿といいね機能はログイン必須
Route::post('/items/{item}/comment', [ItemController::class, 'postComment'])->name('item.comment')->middleware('auth');
Route::post('/items/{item}/like', [ItemController::class, 'postLike'])->name('item.like')->middleware('auth');

Route::get('/register', fn () => view('auth.register'))->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', [LoginUserController::class, 'store']);

Route::get('/email/verified', function () {
    return view('auth.verify-email');
});

Route::middleware(['auth', 'verified', 'first.login'])->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'showEditProfile'])->name('mypage.profile');
    Route::get('/sell', [SellController::class, 'showCreateItem'])->name('sell');

    // 出品フォームの送信もログイン必須になる
    // Route::post('/sell', [SellController::class, 'storeItem']);
});

Route::get('/mypage/profile', [ProfileController::class, 'showEditProfile'])->name('mypage.profile');









