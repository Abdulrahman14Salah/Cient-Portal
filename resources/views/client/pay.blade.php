<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            إتمام الدفع — المرحلة {{ $payment->stage }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-8">

                {{-- ملخص الدفعة --}}
                <div class="mb-6 border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <p class="text-sm text-gray-500">المبلغ المستحق</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">
                        {{ number_format($payment->amount, 2) }}
                        <span class="text-lg font-normal uppercase">{{ $payment->currency }}</span>
                    </p>
                    <p class="text-sm text-gray-400 mt-2">
                        المرحلة {{ $payment->stage }} — رقم العملية #{{ $payment->id }}
                    </p>
                </div>

                {{-- رسائل الخطأ --}}
                <div id="payment-error"
                    class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                </div>

                {{-- رسائل النجاح --}}
                <div id="payment-success"
                    class="hidden mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">
                    تم الدفع بنجاح! جارٍ التحويل...
                </div>

                {{-- Stripe Card Element --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        بيانات البطاقة
                    </label>
                    <div id="card-element"
                        class="border border-gray-300 rounded-md p-3 bg-white focus-within:ring-2 focus-within:ring-indigo-500">
                    </div>
                </div>

                {{-- زر الدفع --}}
                <button id="pay-btn"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50
                               disabled:cursor-not-allowed text-white font-semibold
                               py-3 px-6 rounded-md transition-colors duration-150">
                    ادفع الآن
                </button>

                <style>
                    #pay-btn {
                        color: red;
                        background: black;
                        padding: 9px;
                    }
                </style>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const stripe = Stripe(
                "pk_test_51TC7sQEHdLgw1BxiZ97UIupdeX1W5XAG0xyAAx5cxobTrRV9kGAt6kgWNpp5Z2R4MErvVyQDWO2xiVeHgInAW3ru007XbHPYUb"
            );

            const elements = stripe.elements();

            const card = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#374151',
                        '::placeholder': {
                            color: '#9CA3AF'
                        },
                    },
                    invalid: {
                        color: '#DC2626'
                    },
                }
            });

            card.mount('#card-element');

            // عرض أخطاء الكارت
            card.on('change', ({
                error
            }) => {
                const errorEl = document.getElementById('payment-error');

                if (error) {
                    errorEl.textContent = error.message;
                    errorEl.classList.remove('hidden');
                } else {
                    errorEl.classList.add('hidden');
                }
            });

            document.getElementById('pay-btn').addEventListener('click', async () => {
                const btn = document.getElementById('pay-btn');
                const errorEl = document.getElementById('payment-error');

                btn.disabled = true;
                btn.textContent = 'جارٍ المعالجة...';
                errorEl.classList.add('hidden');

                try {
                    // 1. اطلب client_secret
                    const res = await fetch('{{ route('client.payments.intent', $payment) }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });

                    const data = await res.json();

                    console.log("Intent response:", data); // 🔥 debug

                    if (!res.ok || !data.client_secret) {
                        throw new Error(data.message ?? 'حدث خطأ، حاول مجدداً.');
                    }

                    // 2. تأكيد الدفع
                    const result = await stripe.confirmCardPayment(data.client_secret, {
                        payment_method: {
                            card: card // ✅ التصحيح هنا
                        }
                    });

                    console.log("Stripe result:", result); // 🔥 debug

                    if (result.error) {
                        throw new Error(result.error.message);
                    }

                    if (result.paymentIntent.status === 'succeeded') {
                        document.getElementById('payment-success').classList.remove('hidden');

                        setTimeout(() => {
                            window.location.href = '{{ route('client.dashboard') }}';
                        }, 2000);
                    }

                } catch (err) {
                    console.error(err); // 🔥 debug

                    errorEl.textContent = err.message;
                    errorEl.classList.remove('hidden');

                    btn.disabled = false;
                    btn.textContent = 'ادفع الآن';
                }
            });
        </script>
    @endpush

</x-app-layout>
