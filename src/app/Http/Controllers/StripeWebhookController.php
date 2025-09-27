<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Transaction;
use Stripe\Stripe;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $item_id = $session->metadata->item_id ?? null;

            if ($item_id) {
                $item = Item::find($item_id);
                if ($item && !$item->is_sold) {
                    $item->is_sold = true;
                    $item->save();

                    Transaction::create([
                        'item_id' => $item->id,
                        'buyer_id' => $session->client_reference_id ?? null,
                        'seller_id' => $item->user_id,
                        'status' => 'completed',
                        'payment_method' => $session->payment_method_types[0],
                        'stripe_checkout_session_id' => $session->id,
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'shipping_address' => '未設定',
                        'completed_at' => now(),
                    ]);
                }
            }
        }

        return response('Webhook received', 200);
    }
}
