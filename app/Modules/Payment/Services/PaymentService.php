<?php

namespace App\Modules\Payment\Services;

use App\Modules\Integration\Stripe\StripeService;
use App\Modules\Payment\Models\Payment;

class PaymentService
{
    public function __construct(private StripeService $stripe) {}

    public function createPaymentIntent(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return null;
        }

        if ($payment->stripe_payment_intent_id && $payment->stripe_client_secret) {
            return $payment->stripe_client_secret;
        }

        $intent = $this->stripe->createPaymentIntent($payment);

        $payment->update([
            'stripe_payment_intent_id' => $intent->id,
            'stripe_client_secret' => $intent->client_secret,
        ]);

        return $intent->client_secret;
    }
}
