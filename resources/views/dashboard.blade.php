<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



<!DOCTYPE html>
<html>

<head>
    <title>Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body>

    <h2>Pay Now</h2>

    <div id="card-element"></div>
    <button id="payBtn">Pay</button>

    <script>
        const stripe = Stripe(
            "pk_test_51TC7sQEHdLgw1BxiZ97UIupdeX1W5XAG0xyAAx5cxobTrRV9kGAt6kgWNpp5Z2R4MErvVyQDWO2xiVeHgInAW3ru007XbHPYUb"
        );
        const elements = stripe.elements();

        const card = elements.create("card");
        card.mount("#card-element");

        document.getElementById("payBtn").addEventListener("click", async () => {


            const res = await fetch("/payments/1/intent", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await res.json();

            console.log(data);

            const result = await stripe.confirmCardPayment(data.client_secret, {
                payment_method: {
                    card: card
                }
            });

            if (result.error) {
                alert(result.error.message);
            } else {
                if (result.paymentIntent.status === "succeeded") {
                    window.location.href = "/payment-success";
                }
            }
        });
    </script>

</body>

</html>
