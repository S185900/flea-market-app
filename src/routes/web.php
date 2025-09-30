<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\LoginUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\AddressController;

// 未認証でもアクセス可能な商品一覧ページ(おすすめタブがデフォルトで表示される)
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細ページ
Route::get('/item/{item_id}', [ItemController::class, 'showItemDetail'])->name('item.detail');

// コメント投稿といいね機能はログイン必須
Route::post('/items/{item}/comment', [ItemController::class, 'postComment'])->name('item.comment')->middleware('auth');
Route::post('/items/{item}/like', [ItemController::class, 'postLike'])->name('item.like')->middleware('auth');

// 購入手続き画面（ログイン必須）
Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchaseForm'])
    ->name('purchase.form')
    ->middleware('auth');

Route::post('/purchase/{item}', [PurchaseController::class, 'confirm'])->name('purchase.confirm');

Route::post('/purchase/{item}/stripe', [PurchaseController::class, 'redirectToStripe'])->name('purchase.stripe');
Route::get('/purchase/success', [PurchaseController::class, 'handleSuccess'])->name('purchase.success');
Route::get('/purchase/cancel', fn () => view('purchase_cancel'))->name('purchase.cancel');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);


Route::middleware('auth')->group(function () {
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'showEditAddress'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'updateAddress'])->name('address.update');
});



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











