<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\Transaction;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    // Stripe Webhookの処理
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

            $transaction = Transaction::where('stripe_checkout_session_id', $session->id)->first();

            if ($transaction && !$transaction->completed_at) {
                $transaction->update([
                    'status' => 'completed',
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'completed_at' => now(),
                ]);

                $item = Item::find($transaction->item_id);
                if ($item && $item->status !== 'sold') {
                    $item->update(['status' => 'sold']);
                }
            }
        }

        return response('Webhook received', 200);
    }
}
