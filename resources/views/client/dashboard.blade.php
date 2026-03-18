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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div>

                        @if (auth()->check())
                            <h2 class="text-lg font-semibold text-gray-900">
                                Welcome, {{ auth()->user()->name }}
                            </h2>
                            <p class="text-sm text-gray-900 opacity-80">
                                {{ auth()->user()->email }}
                            </p>
                        @else
                            NOT LOGGED IN ❌
                        @endif



                    </div>
                </div>
                <div class="p-6 text-gray-900">

                    <div class="mt-4 flex gap-6 text-sm text-gray-900">
                        <span class="border-b-2 border-white pb-1">Overview</span>
                        <span class="opacity-70">Tasks</span>
                        <span class="opacity-70">Uploads</span>
                        <span class="opacity-70">Documents</span>
                        <span class="opacity-70">FAQ</span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @php
        $totalStages = 3;
        $paidStages = collect($payments)->where('status', 'paid')->count();
        $progress = ($paidStages / $totalStages) * 100;
    @endphp

    <div class="bg-white p-4 rounded-xl shadow-sm mb-6">
        <p class="text-sm text-gray-600 mb-2">Visa Progress</p>

        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $progress }}%"></div>
        </div>

        <p class="text-xs text-gray-500 mt-2">
            {{ $paidStages }} / {{ $totalStages }} completed
        </p>
    </div>

    <div>
        @foreach ($payments as $payment)
            <x-payment-card :payment="$payment" />
        @endforeach
    </div>

</x-app-layout>
