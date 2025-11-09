<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
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

// 商品一覧ページ(おすすめタブの方がデフォルトで表示される)
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細ページ
Route::get('/item/{item_id}', [ItemController::class, 'showItemDetail'])->name('item.detail');

//
// 認証関連（ログイン・登録・メール確認・ログアウト）
//

Route::get('/register', fn () => view('auth.register'))->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', [LoginUserController::class, 'store']);

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['web', 'auth'])
    ->name('logout');

// メール認証案内ページ（未認証ユーザーがログインした場合）
Route::get('/email/verify/notice', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('mypage.create'); // 認証済みならプロフィール設定へ
    }

    // 初回表示時に認証メールを送信
    $request->user()->sendEmailVerificationNotification();

    return view('auth.verify-email'); // 未認証なら案内ページを表示
})->middleware('auth')->name('verification.notice.notice');

// メール認証リンクからの遷移
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // 認証完了
    return redirect()->route('mypage.create'); // 認証後にプロフィール設定へ
})->middleware(['auth', 'signed'])->name('verification.verify');

// メール認証再送信
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送信しました！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');



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
Route::middleware(['auth', 'verified'])->group(function () {
    // 購入手続き画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchaseForm'])->name('purchase.form');
    Route::post('/purchase/{item}', [PurchaseController::class, 'confirm'])->name('purchase.confirm');
    Route::post('/purchase/{item}/stripe', [PurchaseController::class, 'redirectToStripe'])->name('purchase.stripe');

    Route::post('/purchase/{item}/prepare', [PurchaseController::class, 'redirectToStripe'])->name('purchase.prepare');

    // 住所編集
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'showEditAddress'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'updateAddress'])->name('address.update');
});


// プロフィール登録（初回ログイン時のみ）
Route::middleware(['auth', 'verified', 'first.login'])->group(function () {
    Route::get('/mypage/create', [ProfileController::class, 'showCreateProfileForm'])->name('mypage.create');
    Route::post('/mypage/create', [ProfileController::class, 'storeProfile'])->name('mypage.store');
});


// 認証済みユーザー
Route::middleware(['auth', 'verified'])->group(function () {

    // プロフィール画面（一覧・概要）
    Route::get('/mypage', [ProfileController::class, 'showProfileIndex'])->name('mypage.index');

    // プロフィール編集画面（設定）
    Route::get('/mypage/profile', [ProfileController::class, 'showEditProfile'])->name('mypage.profile');
    Route::patch('/mypage/profile', [ProfileController::class, 'updateProfile'])->name('mypage.profile.update');

});

// 出品関連（ログイン必須）
Route::middleware(['auth', 'verified'])->group(function () {
    // 出品画面表示
    Route::get('/sell', [SellController::class, 'showCreateItem'])->name('sell');

    // 出品処理
    Route::post('/sell', [SellController::class, 'storeItem'])->name('sell.store');
});










