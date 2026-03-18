<?php

namespace App\Modules\Integration\Stripe;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent($payment)
    {
        $intent = PaymentIntent::create([
            'amount' => $payment->amount * 100, // cents
            'currency' => $payment->currency,
            'metadata' => [
                'payment_id' => $payment->id,
            ],
        ]);

        return $intent;
    }
}
