<?php

namespace App\Modules\Payment\Services;

use App\Modules\Integration\Stripe\StripeService;
use App\Modules\Payment\Models\Payment;
use Stripe\Stripe;

class PaymentService
{
    private StripeService $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    public function createPaymentIntent(Payment $payment): ?string
    {
        if ($payment->status !== 'pending') {
            return null;
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));


        $intent = $this->stripe->createPaymentIntent($payment);


        $payment->update([
            'stripe_payment_intent_id' => $intent->id,
            'stripe_client_secret' => $intent->client_secret,
        ]);

        return $intent->client_secret;
    }
}
