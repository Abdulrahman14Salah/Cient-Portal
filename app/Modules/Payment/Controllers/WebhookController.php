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

        // Handle events
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
     *  Payment succeeded
     */
    private function handlePaymentSucceeded($intent)
    {
        \Log::info('PaymentIntent succeeded', [
            'intent_id' => $intent->id,
        ]);

        $payment = Payment::where('stripe_payment_intent_id', $intent->id)->first();

        if (!$payment) {
            \Log::warning('Payment not found', ['intent' => $intent->id]);
            return;
        }

        // لو already paid متعملش حاجة
        if ($payment->status === 'paid') {
            return;
        }

        //  تحديث الدفع
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // 🔓 فتح المرحلة اللي بعدها
        if ($payment->stage == 1) {
            Payment::where('application_id', $payment->application_id)
                ->where('stage', 2)
                ->update(['status' => 'pending']);
        }

        if ($payment->stage == 2) {
            Payment::where('application_id', $payment->application_id)
                ->where('stage', 3)
                ->update(['status' => 'pending']);
        }
    }

    /**
     ** Payment failed
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

        if ($payment->status === 'paid') {
            return;
        }

        $payment->update([
            'status' => 'failed',
        ]);
    }

    /**
     ** Unlock next stage logic
     */
    private function unlockNextStage(Payment $payment): void
    {
        Payment::where('case_id', $payment->case_id)
            ->where('stage', $payment->stage + 1)
            ->where('status', 'locked')
            ->update(['status' => 'pending']);
    }
}
