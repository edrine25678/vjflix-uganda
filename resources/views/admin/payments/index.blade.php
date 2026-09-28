@php
    // Full literal class names: Tailwind's JIT scanner cannot see interpolated
    // class names such as bg-{{ $color }}-500/20, so these must be spelled out.
    $statusStyles = [
        'completed' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
        'pending' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
        'failed' => 'bg-red-500/20 text-red-400 border-red-500/30',
        'cancelled' => 'bg-slate-500/20 text-slate-400 border-slate-500/30',
    ];

    $networkStyles = [
        'mtn' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
        'airtel' => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
    ];
@endphp

<x-admin-layout>
    <x-slot:title>Payments — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-7xl mx-auto">
        <div>
            <h1 class="text-2xl font-extrabold text-white font-display">
                Mobile Money Payments
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">Every MTN and Airtel Money charge attempt, including failures. Read-only — status is driven by the payment provider callback.</p>
        </div>

        <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/60 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-3.5">Reference</th>
                            <th class="px-6 py-3.5">Customer</th>
                            <th class="px-6 py-3.5">Plan</th>
                            <th class="px-6 py-3.5">Amount</th>
                            <th class="px-6 py-3.5">Method</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Date</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse ($payments as $payment)
                            @php
                                $statusClass = $statusStyles[$payment->status] ?? $statusStyles['pending'];
                                $networkClass = $networkStyles[strtolower((string) $payment->network)] ?? 'bg-slate-500/20 text-slate-400 border-slate-500/30';
                            @endphp
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-slate-200">{{ $payment->transaction_reference }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($payment->user)
                                        <span class="font-semibold text-white block">{{ $payment->user->name }}</span>
                                        <span class="text-[11px] text-slate-400 font-mono">{{ $payment->user->phone_number }}</span>
                                    @else
                                        <span class="text-slate-500">Deleted user</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ $payment->plan?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-white">{{ $payment->formattedAmount() }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 rounded {{ $networkClass }} border px-2 py-0.5 text-[10px] font-bold">
                                        {{ $payment->networkLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 rounded {{ $statusClass }} border px-2 py-0.5 text-[10px] font-bold">
                                        @if ($payment->status === 'completed')
                                            <x-bi-check-circle-fill class="h-3 w-3" />
                                        @elseif ($payment->status === 'failed')
                                            <x-bi-x-circle-fill class="h-3 w-3" />
                                        @elseif ($payment->status === 'cancelled')
                                            <x-bi-dash-circle-fill class="h-3 w-3" />
                                        @else
                                            <x-bi-clock class="h-3 w-3" />
                                        @endif
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-400">
                                    {{ $payment->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-colors">
                                        <x-bi-eye class="h-3 w-3" />
                                        Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    No payments recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
