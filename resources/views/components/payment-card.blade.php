<div>
    <h3>Stage {{ $payment->stage }}</h3>

    @if ($payment->status === 'pending')
        <a href="{{ url('/pay?payment_id=' . $payment->id) }}">Pay</a>
    @elseif($payment->status === 'paid')
        <span>Paid ✅</span>
    @else
        <span>Locked 🔒</span>
    @endif
</div>
