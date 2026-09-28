@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('content')
<div class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-4">Choose Your Plan</h1>
            <p class="text-gray-400 text-lg">Unlock unlimited entertainment with VJFlix Uganda</p>
        </div>

        @if($activeSubscription)
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-6 mb-8 text-white">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">Active Subscription: {{ $activeSubscription->plan->name }}</h2>
                        <p class="opacity-90">Expires on {{ $activeSubscription->expires_at->format('F j, Y') }}</p>
                        <p class="opacity-90 mt-1">{{ $activeSubscription->daysRemaining() }} days remaining</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('subscriptions.history') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition">
                            View History
                        </a>
                        @if($activeSubscription->isActive())
                            <form action="{{ route('subscriptions.cancel', $activeSubscription) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription?')">
                                @csrf
                                <button type="submit" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition">
                                    Cancel Subscription
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($plans as $plan)
                <div class="bg-gray-800 rounded-2xl p-6 border-2 {{ $activeSubscription && $activeSubscription->plan_id === $plan->id ? 'border-purple-500' : 'border-transparent' }} hover:border-purple-500 transition duration-300 relative">
                    @if($plan->badge)
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                            <span class="bg-purple-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
                                {{ $plan->badge }}
                            </span>
                        </div>
                    @endif

                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-white mb-2">{{ $plan->name }}</h3>
                        <div class="text-4xl font-bold text-white mb-2">
                            {{ $plan->formattedPrice() }}
                        </div>
                        <p class="text-gray-400">{{ $plan->durationLabel() }}</p>
                    </div>

                    <ul class="space-y-3 mb-6">
                        @foreach($plan->features ?? [] as $feature)
                            <li class="flex items-center text-gray-300">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('subscriptions.plan', $plan) }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center font-semibold py-3 rounded-lg transition">
                        {{ $activeSubscription && $activeSubscription->plan_id === $plan->id ? 'Current Plan' : ($plan->price_ugx === 0 ? 'Get Started' : 'Subscribe Now') }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
