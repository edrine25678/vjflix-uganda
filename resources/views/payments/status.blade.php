<x-layout>
    <x-slot:title>Payment Status</x-slot:title>

    <x-header />

<div class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-gray-800 rounded-2xl p-8 text-center">
            @if($payment->status === 'completed')
                <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-4">Payment Successful!</h1>
                <p class="text-gray-400 mb-6">Your subscription has been activated successfully.</p>
            @elseif($payment->status === 'failed')
                <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-4">Payment Failed</h1>
                <p class="text-gray-400 mb-6">
                    @if($payment->failure_reason)
                        {{ $payment->failure_reason }}
                    @else
                        Your payment could not be processed. Please try again.
                    @endif
                </p>
            @else
                <div class="w-20 h-20 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-4">Payment Pending</h1>
                <p class="text-gray-400 mb-6">Please complete the payment on your phone. We'll verify it automatically.</p>
            @endif

            <div class="bg-gray-700 rounded-lg p-4 mb-6 text-left">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-400">Transaction Reference:</span>
                    <span class="text-white font-mono">{{ $payment->transaction_reference }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-400">Amount:</span>
                    <span class="text-white">{{ $payment->formattedAmount() }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-400">Payment Method:</span>
                    <span class="text-white">{{ $payment->networkLabel() }}</span>
                </div>
                @if($payment->phone_number)
                    <div class="flex justify-between">
                        <span class="text-gray-400">Phone Number:</span>
                        <span class="text-white">{{ $payment->phone_number }}</span>
                    </div>
                @endif
            </div>

            @if($payment->status === 'pending')
                <div class="mb-6">
                    <button onclick="checkPaymentStatus()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition mr-3">
                        Check Status
                    </button>
                    <a href="{{ route('subscriptions.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition">
                        Back to Plans
                    </a>
                </div>
            @else
                <a href="{{ route('subscriptions.index') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg transition">
                    Go to Dashboard
                </a>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function checkPaymentStatus() {
    const reference = '{{ $payment->transaction_reference }}';
    
    fetch('{{ route('payments.check', $payment->transaction_reference) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'completed') {
                    window.location.reload();
                } else if (data.status === 'failed') {
                    window.location.reload();
                } else {
                    alert('Payment is still pending. Please check again in a moment.');
                }
            } else {
                alert('Unable to check payment status. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
}

// Auto-check every 10 seconds if payment is pending
@if($payment->status === 'pending')
setInterval(checkPaymentStatus, 10000);
@endif
</script>
@endpush
</x-layout>

