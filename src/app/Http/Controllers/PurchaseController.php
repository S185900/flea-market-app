<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    public function showPurchaseForm($item_id)
    {
        $item = Item::with('images', 'brand')->findOrFail($item_id);
        $user = Auth::user();

        $selectedMethod = session('selected_payment_method');

        return view('purchase_confirm', [
            'item' => $item,
            'user' => $user,
            'address' => $user->shipping_address,
            'selectedMethod' => $selectedMethod,
        ]);
    }

    public function confirm(Request $request, Item $item)
    {
        $selectedMethod = $request->input('payment_method');
        $shippingAddress = $request->input('shipping_address');

        // 変更：支払い方法と配送先をセッションに保存
        session([
            'selected_payment_method' => $selectedMethod,
            'shipping_address' => $shippingAddress,
        ]);

        return view('purchase_confirm', [
            'item' => $item,
            'user' => auth()->user(),
            'selectedMethod' => $selectedMethod,
            'address' => $shippingAddress,
        ]);
    }

    public function redirectToStripe(PurchaseRequest $request, Item $item)
    {
        $selectedMethod = $request->input('payment_method');
        $shippingAddress = $request->input('shipping_address');

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
                'konbini' => ['expires_after_days' => 3],
            ] : [],

            // Stripe完了後に商品一覧へ戻す
            // 'success_url' => route('purchase.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            // 'cancel_url' => route('purchase.cancel', [], true),

            // ngrock使用のため動的にURLを生成
            'success_url' => url('/purchase/success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => url('/purchase/cancel'),


        ]);

        // 事前にTransactionを作成（completedとして保存）
        \App\Models\Transaction::create([
            'item_id' => $item->id,
            'buyer_id' => auth()->id(),
            'seller_id' => $item->user_id,
            'status' => 'completed',
            'payment_method' => $selectedMethod,
            'stripe_checkout_session_id' => $session->id,
            'shipping_address' => $shippingAddress,
            'completed_at' => now(),
        ]);

        // 商品ステータス更新
        $item->update(['status' => 'sold']);

        return redirect($session->url); // Stripe決済画面へ遷移するだけ
    }


    // 購入成功後の処理
    public function handleSuccess(Request $request)
    {
        $session_id = $request->get('session_id');

        if (!$session_id) {
            return redirect()->route('items.index')->withErrors(['message' => 'セッションIDが見つかりません']);
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::retrieve($session_id);

        // コンビニ支払いはWebhookで処理する
        if ($session->payment_method_types[0] !== 'card') {
            return redirect()->route('items.index')->with('message', '支払い手続きが完了しました');
        }

        $item_id = $session->metadata->item_id ?? null;

        if ($item_id) {
            $item = Item::find($item_id);

            // すでにTransactionがあるか確認
            $transaction = \App\Models\Transaction::where('stripe_checkout_session_id', $session_id)->first();

            if ($transaction && !$transaction->completed_at) {
                $transaction->update([
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                if ($item && $item->status !== 'sold') {
                    $item->update(['status' => 'sold']);
                }
            }
        }

        return redirect()->route('items.index')->with('message', '購入が完了しました');
    }


}
