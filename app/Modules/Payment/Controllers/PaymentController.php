<?php

namespace App\Modules\Payment\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service) {}

    public function createIntent(Payment $payment): JsonResponse
    {

        abort_if($payment->user_id !== auth()->id(), 403);

        try {

            $clientSecret = $this->service->createPaymentIntent($payment);

            if (!$clientSecret) {
                return response()->json([
                    'message' => 'Payment not allowed'
                ], 400);
            }

            return response()->json([
                'client_secret' => $clientSecret,
            ]);
        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }
}
