@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
<div class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-white">Payment History</h1>
            <a href="{{ route('subscriptions.index') }}" class="text-purple-400 hover:text-purple-300">
                &larr; Back to Plans
            </a>
        </div>

        <div class="bg-gray-800 rounded-2xl overflow-hidden">
            @if($payments->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-white font-semibold">Reference</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Plan</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Amount</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Method</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Status</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4">
                                    <span class="text-white font-mono text-sm">{{ $payment->transaction_reference }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-300">
                                    {{ $payment->plan ? $payment->plan->name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-white font-medium">
                                    {{ $payment->formattedAmount() }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $payment->network === 'mtn' ? 'bg-amber-600/20 text-amber-400' : 
                                           ($payment->network === 'airtel' ? 'bg-rose-600/20 text-rose-400' : 'bg-gray-600/20 text-gray-400') }}">
                                        {{ $payment->networkLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $payment->status === 'completed' ? 'bg-green-600/20 text-green-400' : 
                                           ($payment->status === 'failed' ? 'bg-red-600/20 text-red-400' : 'bg-yellow-600/20 text-yellow-400') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-300">
                                    {{ $payment->created_at->format('M j, Y g:i A') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $payments->links() }}
            @else
                <div class="p-12 text-center">
                    <div class="text-gray-400 mb-4">No payment history found</div>
                    <a href="{{ route('subscriptions.index') }}" class="text-purple-400 hover:text-purple-300">
                        View Available Plans
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
