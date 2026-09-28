@extends('layouts.app')

@section('title', $plan->name . ' Plan')

@section('content')
<div class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('subscriptions.index') }}" class="text-purple-400 hover:text-purple-300 mb-6 inline-block">
            &larr; Back to Plans
        </a>

        <div class="bg-gray-800 rounded-2xl p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">{{ $plan->name }} Plan</h1>
                    <p class="text-gray-400">{{ $plan->description }}</p>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-bold text-white">{{ $plan->formattedPrice() }}</div>
                    <div class="text-gray-400">{{ $plan->durationLabel() }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Features</h3>
                    <ul class="space-y-2">
                        @foreach($plan->features ?? [] as $feature)
                            <li class="flex items-center text-gray-300">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if($activeSubscription && $activeSubscription->plan_id === $plan->id)
                <div class="bg-green-600/20 border border-green-600 rounded-lg p-4 text-center">
                    <p class="text-green-400 font-semibold">You are currently subscribed to this plan</p>
                </div>
            @elseif($plan->price_ugx === 0)
                <div class="bg-purple-600/20 border border-purple-600 rounded-lg p-4 text-center">
                    <p class="text-purple-400 font-semibold">Free plan - No payment required</p>
                    <a href="{{ route('subscriptions.index') }}" class="inline-block mt-3 bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                        Continue with Free Plan
                    </a>
                </div>
            @else
                <form action="{{ route('payments.initiate') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                    <div>
                        <label class="block text-white font-medium mb-2">Select Payment Method</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="payment_method" value="mtn" class="peer sr-only" checked>
                                <div class="bg-gray-700 peer-checked:bg-amber-600 peer-checked:border-amber-500 border-2 border-transparent rounded-lg p-4 transition">
                                    <div class="text-white font-semibold">MTN Mobile Money</div>
                                    <div class="text-sm text-gray-300 peer-checked:text-white">Pay with MTN MoMo</div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="payment_method" value="airtel" class="peer sr-only">
                                <div class="bg-gray-700 peer-checked:bg-rose-600 peer-checked:border-rose-500 border-2 border-transparent rounded-lg p-4 transition">
                                    <div class="text-white font-semibold">Airtel Money</div>
                                    <div class="text-sm text-gray-300 peer-checked:text-white">Pay with Airtel Money</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="phone_number" class="block text-white font-medium mb-2">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number" 
                            placeholder="07XX XXX XXX" 
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-purple-500"
                            required>
                        <p class="text-gray-400 text-sm mt-2">Enter your MTN or Airtel mobile money number</p>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-4 rounded-lg transition">
                        Pay {{ $plan->formattedPrice() }}
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-gray-800 rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Payment Information</h3>
            <ul class="space-y-2 text-gray-300">
                <li>&bull; All payments are processed securely</li>
                <li>&bull; Your subscription will be activated immediately after payment</li>
                <li>&bull; You can cancel your subscription at any time</li>
                <li>&bull; For support, contact us at support@vjflix.ug</li>
            </ul>
        </div>
    </div>
</div>
@endsection
