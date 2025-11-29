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

// 商品一覧画面
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細画面
Route::get('/item/{item_id}', [ItemController::class, 'showItemDetail'])->name('item.detail');

// コメントの投稿・いいね機能
Route::middleware('auth')->group(function () {
    Route::post('/items/{item}/comment', [ItemController::class, 'postComment'])->name('item.comment');
    Route::post('/items/{item}/like', [ItemController::class, 'postLike'])->name('item.like');
});

// ユーザー認証関連
Route::get('/register', fn () => view('auth.register'))->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', [LoginUserController::class, 'store']);

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['web', 'auth'])
    ->name('logout');

// メール認証誘導画面
Route::get('/email/verify/notice', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('mypage.create');
    }

    return view('auth.verify_email');
})->middleware('auth')->name('verification.notice.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('mypage.create');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back();
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 商品購入画面
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchaseForm'])->name('purchase.form');
    Route::post('/purchase/{item}', [PurchaseController::class, 'confirm'])->name('purchase.confirm');
    Route::post('/purchase/{item}/stripe', [PurchaseController::class, 'redirectToStripe'])->name('purchase.stripe');
    Route::post('/purchase/{item}/prepare', [PurchaseController::class, 'redirectToStripe'])->name('purchase.prepare');

    // 送付先住所変更画面
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'showEditAddress'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'updateAddress'])->name('address.update');
});

// 購入処理・Stripe Webhook関連
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
Route::get('/purchase/success', [PurchaseController::class, 'handleSuccess'])->name('purchase.success');
Route::get('/purchase/cancel', fn () => redirect()->route('items.index'))->name('purchase.cancel');

// プロフィール画面・編集画面
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'showProfileIndex'])->name('mypage.index');
    Route::get('/mypage/profile', [ProfileController::class, 'showEditProfile'])->name('mypage.profile');
    Route::patch('/mypage/profile', [ProfileController::class, 'updateProfile'])->name('mypage.profile.update');
});

// プロフィール設定画面（設定画面/初回）
Route::middleware(['auth', 'verified', 'first.login'])->group(function () {
    Route::get('/mypage/create', [ProfileController::class, 'showCreateProfileForm'])->name('mypage.create');
    Route::post('/mypage/create', [ProfileController::class, 'storeProfile'])->name('mypage.store');
});

// 商品出品画面
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/sell', [SellController::class, 'showCreateItem'])->name('sell');
    Route::post('/sell', [SellController::class, 'storeItem'])->name('sell.store');
});