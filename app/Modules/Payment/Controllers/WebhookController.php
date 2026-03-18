<?php

namespace App\Modules\Payment\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Stripe\Webhook;
use App\Modules\Payment\Models\Payment;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        // 🔥 Handle events
        switch ($event->type) {

            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled event type', ['type' => $event->type]);
        }

        return response('OK', 200);
    }

    /**
     * ✅ Payment succeeded
     */
    private function handlePaymentSucceeded($intent)
    {
        $payment = Payment::where('stripe_payment_intent_id', $intent->id)->first();

        if (!$payment) {
            Log::warning('Payment not found', ['intent' => $intent->id]);
            return;
        }

        // 🔥 IMPORTANT: prevent duplicate processing
        if ($payment->status === 'paid') {
            Log::info('Payment already processed', ['payment_id' => $payment->id]);
            return;
        }

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->unlockNextStage($payment);
    }

    /**
     * ❌ Payment failed
     */
    private function handlePaymentFailed($intent)
    {
        Log::warning('Payment failed', [
            'intent_id' => $intent->id,
        ]);

        $payment = Payment::where('stripe_payment_intent_id', $intent->id)->first();

        if (!$payment) {
            return;
        }

        $payment->update([
            'status' => 'failed',
        ]);
    }

    /**
     * 🔓 Unlock next stage logic
     */
    private function unlockNextStage(Payment $payment)
    {
        // Stage 1 → unlock Stage 2
        if ($payment->stage == 1) {
            Payment::where('case_id', $payment->case_id)
                ->where('stage', 2)
                ->update(['status' => 'pending']);
        }

        // Stage 2 → unlock Stage 3
        if ($payment->stage == 2) {
            Payment::where('case_id', $payment->case_id)
                ->where('stage', 3)
                ->update(['status' => 'pending']);
        }
    }
}
