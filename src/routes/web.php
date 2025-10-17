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

//
// 未認証でもアクセス可能なページ
//

// 商品一覧ページ(おすすめタブがデフォルトで表示される)
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細ページ
Route::get('/item/{item_id}', [ItemController::class, 'showItemDetail'])->name('item.detail');

//
// 認証関連（ログイン・登録・メール確認）
//

Route::get('/register', fn () => view('auth.register'))->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', [LoginUserController::class, 'store']);

Route::get('/email/verified', function () {
    return view('auth.verify-email');
});

// コメント・いいね機能（ログイン必須）
Route::middleware('auth')->group(function () {
    Route::post('/items/{item}/comment', [ItemController::class, 'postComment'])->name('item.comment');
    Route::post('/items/{item}/like', [ItemController::class, 'postLike'])->name('item.like');
});

// Stripe Webhook & 購入完了・キャンセル（認証不要）
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
Route::get('/purchase/success', [PurchaseController::class, 'handleSuccess'])->name('purchase.success');
Route::get('/purchase/cancel', fn () => view('purchase_cancel'))->name('purchase.cancel');

// 購入関連（ログイン必須）
Route::middleware('auth')->group(function () {
    // 購入手続き画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchaseForm'])->name('purchase.form');
    Route::post('/purchase/{item}', [PurchaseController::class, 'confirm'])->name('purchase.confirm');
    Route::post('/purchase/{item}/stripe', [PurchaseController::class, 'redirectToStripe'])->name('purchase.stripe');

    Route::post('/purchase/{item}/prepare', [PurchaseController::class, 'redirectToStripe'])->name('purchase.prepare');

    // 住所編集
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'showEditAddress'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'updateAddress'])->name('address.update');
});


// メール認証実装の時に復活させる
// Route::middleware(['auth', 'verified', 'first.login'])->group(function () {

//     // プロフィールのトップ画面（/mypage）
//     Route::get('/mypage', [ProfileController::class, 'showProfileIndex'])->name('mypage.index');

//     // プロフィール編集画面（/mypage/profile）
//     Route::get('/mypage/profile', [ProfileController::class, 'showEditProfile'])->name('mypage.profile');

//     Route::get('/sell', [SellController::class, 'showCreateItem'])->name('sell');

// });

// メール認証なしで作業のため
Route::middleware(['auth'])->group(function () {

    // プロフィール画面（一覧・概要）
    Route::get('/mypage', [ProfileController::class, 'showProfileIndex'])->name('mypage.index');

    // プロフィール編集画面（設定）
    Route::get('/mypage/profile', [ProfileController::class, 'showEditProfile'])->name('mypage.profile');
    Route::patch('/mypage/profile', [ProfileController::class, 'updateProfile'])->name('mypage.profile.update');

    // 初回プロフィール登録画面
    Route::get('/mypage/create', [ProfileController::class, 'showCreateProfileForm'])->name('mypage.create');
    Route::post('/mypage/create', [ProfileController::class, 'storeProfile'])->name('mypage.store');
});

// 出品関連（ログイン必須）
Route::middleware('auth')->group(function () {
    // 出品画面表示
    Route::get('/sell', [SellController::class, 'showCreateItem'])->name('sell');

    // 出品処理
    Route::post('/sell', [SellController::class, 'storeItem'])->name('sell.store');
});










