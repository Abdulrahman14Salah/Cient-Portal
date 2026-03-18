@if ($payment->status === 'pending')
    <button onclick="pay({{ $payment->id }})">Pay</button>
@elseif($payment->status === 'paid')
    <span style="color: green;">Paid ✅</span>
@else
    <span style="color: gray;">Locked 🔒</span>
@endif
