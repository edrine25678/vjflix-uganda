<x-layout>
    <x-slot:title>Subscription History</x-slot:title>

    <x-header />

<div class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-white">Subscription History</h1>
            <a href="{{ route('subscriptions.index') }}" class="text-purple-400 hover:text-purple-300">
                &larr; Back to Plans
            </a>
        </div>

        <div class="bg-gray-800 rounded-2xl overflow-hidden">
            @if($subscriptions->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-white font-semibold">Plan</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Status</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Start Date</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Expiry Date</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Payment Method</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($subscriptions as $subscription)
                            <tr class="hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4">
                                    <div class="text-white font-medium">{{ $subscription->plan->name }}</div>
                                    <div class="text-gray-400 text-sm">{{ $subscription->plan->formattedPrice() }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $subscription->status === 'active' ? 'bg-green-600/20 text-green-400' : 
                                           ($subscription->status === 'cancelled' ? 'bg-red-600/20 text-red-400' : 'bg-yellow-600/20 text-yellow-400') }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-300">
                                    {{ $subscription->starts_at ? $subscription->starts_at->format('M j, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-gray-300">
                                    {{ $subscription->expires_at ? $subscription->expires_at->format('M j, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-gray-300">
                                    {{ ucfirst($subscription->payment_method ?? 'N/A') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $subscriptions->links() }}
            @else
                <div class="p-12 text-center">
                    <div class="text-gray-400 mb-4">No subscription history found</div>
                    <a href="{{ route('subscriptions.index') }}" class="text-purple-400 hover:text-purple-300">
                        View Available Plans
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
</x-layout>

