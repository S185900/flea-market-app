<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    private const KONBINI_EXPIRES_AFTER_DAYS = 3;

    // 購入確認画面の表示
    public function showPurchaseForm($item_id)
    {
        $item = Item::with('images', 'brand')->findOrFail($item_id);
        $user = Auth::user();

        $selectedMethod = session('selected_payment_method');

        $fullAddress = "{$user->postal_code} {$user->shipping_address} {$user->building_name}";

        return view('purchase_confirm', [
            'item' => $item,
            'user' => $user,
            'fullAddress' => $fullAddress,
            'selectedMethod' => $selectedMethod,
        ]);
    }

    // 商品購入画面での支払い方法選択の反映
    public function confirm(Request $request, Item $item)
    {
        $selectedMethod = $request->input('payment_method');

        session([
            'selected_payment_method' => $selectedMethod,
        ]);

        $user = auth()->user();
        $fullAddress = "{$user->postal_code} {$user->shipping_address} {$user->building_name}";

        return view('purchase_confirm', [
            'item' => $item,
            'user' => $user,
            'selectedMethod' => $selectedMethod,
            'fullAddress' => $fullAddress,
        ]);
    }

    // Stripeへのリダイレクト処理
    public function redirectToStripe(PurchaseRequest $request, Item $item)
    {
        $selectedMethod = $request->input('payment_method');

        $user = auth()->user();
        $fullAddress = "{$user->postal_code} {$user->shipping_address} {$user->building_name}";

        // テスト環境ではStripe APIをスキップしてダミーのレスポンスを返す
        if (app()->environment('testing')) {

            Purchase::create([
                'item_id' => $item->id,
                'buyer_id' => $user->id,
                'seller_id' => $item->user_id,
                'status' => 'completed',
                'payment_method' => $selectedMethod,
                'stripe_checkout_session_id' => 'test_session_id',
                'shipping_address' => $fullAddress,
                'completed_at' => now(),
            ]);

            $item->update(['status' => 'sold']);

            return response()->json([
                'checkout_url' => 'https://stripe.com/mock-checkout',
            ]);
        }

        // 本番環境ではStripe APIを呼び出す
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => [$selectedMethod === 'card' ? 'card' : 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->title,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'item_id' => $item->id,
            ],
            'payment_method_options' => $selectedMethod === 'convenience' ? [
                'konbini' => [
                    'expires_after_days' => self::KONBINI_EXPIRES_AFTER_DAYS,
                ],
            ] : [],
            'success_url' => url('/purchase/success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => url('/purchase/cancel'),
        ]);

        Purchase::create([
            'item_id' => $item->id,
            'buyer_id' => $user->id,
            'seller_id' => $item->user_id,
            'status' => 'completed',
            'payment_method' => $selectedMethod,
            'stripe_checkout_session_id' => $session->id,
            'shipping_address' => $fullAddress,
            'completed_at' => now(),
        ]);

        $item->update(['status' => 'sold']);

        return response()->json([
            'checkout_url' => $session->url,
        ]);
    }

    // 購入成功後の処理
    public function handleSuccess(Request $request)
    {
        $session_id = $request->get('session_id');

        if (!$session_id) {
            return redirect()->route('items.index');
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::retrieve($session_id);

        if ($session->payment_method_types[0] !== 'card') {
            return redirect()->route('items.index');
        }

        $item_id = $session->metadata->item_id ?? null;

        if ($item_id) {
            $item = Item::find($item_id);

            $purchase = Purchase::where('stripe_checkout_session_id', $session_id)->first();

            if ($purchase && !$purchase->completed_at) {
                $purchase->update([
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                if ($purchase && !$purchase->completed_at) {
                    $purchase->update(['status' => 'sold']);
                }
            }
        }

        return redirect()->route('items.index');
    }
}