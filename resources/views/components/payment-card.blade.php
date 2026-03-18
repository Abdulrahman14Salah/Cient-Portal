<div class="border rounded-lg p-4">
    <h3 class="font-semibold text-gray-800">Sage {{ $payment->stage }}</h3>
    <p class="text-gray-600 mt-1">{{ number_format($payment->amount, 2) }} {{ strtoupper($payment->currency) }}</p>

    <div class="mt-3">
        @if ($payment->status === 'pending')
            <a href="{{ route('client.payments.pay', $payment) }}" id="pay-btn"
                class="inline-block bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700">
                ادفع الآن
            </a>
        @elseif ($payment->status === 'paid')
            <span class="text-green-600 font-medium">✅ تم الدفع</span>
        @else
            <span class="text-gray-400">🔒 غير متاح بعد</span>
        @endif
    </div>
</div>
