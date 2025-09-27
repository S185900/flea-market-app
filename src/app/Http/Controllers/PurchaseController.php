<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;

class PurchaseController extends Controller
{
    public function showPurchaseForm($item_id)
    {
        $item = Item::with('images', 'brand')->findOrFail($item_id);
        $user = Auth::user();

        return view('purchase_confirm', [
            'item' => $item,
            'user' => $user,
            'address' => $user->address, // プロフィールに登録済みの住所
            'selectedMethod' => null, // 初期状態では支払い方法は未選択
        ]);
    }

    public function confirm(Request $request, Item $item)
    {
        $selectedMethod = $request->input('payment_method');

        return view('purchase_confirm', [
            'item' => $item,
            'user' => auth()->user(),
            'selectedMethod' => $selectedMethod,
        ]);
    }


    public function redirectToStripe(Request $request, Item $item)
    {
        $selectedMethod = $request->input('payment_method');

        if ($selectedMethod === 'card') {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
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

                // 商品IDをメタデータに含めておくと便利（Stripeセッション作成時に設定）
                'metadata' => [
                    'item_id' => $item->id,
                ],

                'success_url' => route('purchase.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('purchase.cancel', [], true),
            ]);

            return redirect($session->url);
        } elseif ($selectedMethod === 'convenience') {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['konbini'],
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

                // 商品IDをメタデータに含めておくと便利（Stripeセッション作成時に設定）
                'metadata' => [
                    'item_id' => $item->id,
                ],

                'payment_method_options' => [
                    'konbini' => [
                        'expires_after_days' => 3,
                    ],
                ],
                
                'success_url' => route('purchase.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('purchase.cancel', [], true),
            ]);

            return redirect($session->url);
        } else {
            return back()->withErrors(['message' => '未対応の支払い方法です']);
        }
    }

    // カード支払い専用（成功時の処理）
    public function handleSuccess(Request $request)
    {
        $session_id = $request->get('session_id');

        if (!$session_id) {
            return redirect()->route('items.index')->withErrors(['message' => 'セッションIDが見つかりません']);
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::retrieve($session_id);

        // カード支払いのみ処理（コンビニはWebhookで処理）
        if ($session->payment_method_types[0] !== 'card') {
            return redirect()->route('items.index')->with('message', '支払い手続きが完了しました');
        }

        $payment_intent_id = $session->payment_intent;
        $payment_intent = PaymentIntent::retrieve($payment_intent_id);
        $item_id = $session->metadata->item_id ?? null;

        if ($item_id) {
            $item = Item::find($item_id);
            if ($item) {
                $item->is_sold = true;
                $item->save();

                \App\Models\Transaction::create([
                    'item_id' => $item->id,
                    'buyer_id' => auth()->id(),
                    'seller_id' => $item->user_id,
                    'status' => 'completed',
                    'payment_method' => 'card',
                    'stripe_checkout_session_id' => $session_id,
                    'stripe_payment_intent_id' => $payment_intent_id,
                    'shipping_address' => auth()->user()->shipping_address,
                    'completed_at' => now(),
                ]);
            }
        }

        return redirect()->route('items_index')->with('message', '購入が完了しました');
    }






}
