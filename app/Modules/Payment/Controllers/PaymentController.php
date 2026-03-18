<?php

namespace App\Modules\Payment\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service) {}

    /**
     * عرض صفحة الدفع
     */
    public function show(Payment $payment): View|RedirectResponse
    {
        // تحقق من الملكية
        abort_if($payment->user_id !== auth()->id(), 403);

        // لو مش pending، مفيش داعي تفتح الصفحة
        if ($payment->status === 'paid') {
            return redirect()->route('client.dashboard')
                ->with('info', 'هذه الدفعة تمت بالفعل.');
        }

        if ($payment->status !== 'pending') {
            return redirect()->route('client.dashboard')
                ->with('error', 'هذه الدفعة غير متاحة للدفع حالياً.');
        }

        return view('client.pay', [
            'payment'        => $payment,
            'stripePublicKey' => config('services.stripe.key'),
        ]);
    }

    /**
     * إنشاء PaymentIntent وإرجاع client_secret
     */
    public function createIntent(Payment $payment): JsonResponse
    {
        abort_if($payment->user_id !== auth()->id(), 403);

        try {
            $clientSecret = $this->service->createPaymentIntent($payment);

            if (!$clientSecret) {
                return response()->json(['message' => 'Payment not allowed'], 400);
            }

            return response()->json(['client_secret' => $clientSecret]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }
}
