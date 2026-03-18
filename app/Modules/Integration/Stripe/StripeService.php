<?php

namespace App\Modules\Integration\Stripe;

use App\Modules\Payment\Models\Payment;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(Payment $payment): \Stripe\PaymentIntent
    {
        return PaymentIntent::create([
            'amount'   => (int) round($payment->amount * 100),
            'currency' => $payment->currency,
            'metadata' => ['payment_id' => $payment->id],
            'payment_method_types' => ['card'],
        ]);
    }
}
