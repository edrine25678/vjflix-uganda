<x-admin-layout>
    <x-slot:title>Payment {{ $payment->transaction_reference }} — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-5xl mx-auto">
        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <x-bi-arrow-left class="h-3.5 w-3.5" />
            Back to Payments
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white font-display">Payment Details</h1>
                <p class="text-xs text-slate-400 mt-0.5 font-mono">{{ $payment->transaction_reference }}</p>
            </div>

            @php
                $statusClass = match ($payment->status) {
                    'completed' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                    'failed' => 'bg-red-500/20 text-red-400 border-red-500/30',
                    'cancelled' => 'bg-slate-500/20 text-slate-400 border-slate-500/30',
                    default => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                };
            @endphp
            <span class="self-start inline-flex items-center gap-1.5 rounded-xl {{ $statusClass }} border px-3 py-1.5 text-xs font-extrabold uppercase tracking-wider">
                {{ ucfirst($payment->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 space-y-5">
                <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5">
                    <h2 class="text-sm font-extrabold text-white font-display mb-4">Transaction</h2>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                        @foreach ([
                            'Amount' => $payment->formattedAmount(),
                            'Method' => $payment->networkLabel(),
                            'MSISDN' => $payment->phone_number ?: '—',
                            'External reference' => $payment->external_reference ?: '—',
                            'Initiated' => $payment->created_at->format('M j, Y g:i A'),
                            'Paid at' => $payment->paid_at?->format('M j, Y g:i A') ?? '—',
                        ] as $label => $value)
                            <div>
                                <dt class="text-slate-500 font-semibold">{{ $label }}</dt>
                                <dd class="mt-0.5 text-slate-200 font-semibold break-words">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                @if ($payment->failure_reason)
                    <div class="rounded-2xl bg-red-500/5 border border-red-500/30 p-5">
                        <h2 class="flex items-center gap-2 text-sm font-extrabold text-red-400 font-display mb-2">
                            <x-bi-exclamation-circle-fill class="h-4 w-4" />
                            Failure reason
                        </h2>
                        <p class="text-xs text-red-300/80 leading-relaxed">{{ $payment->failure_reason }}</p>
                    </div>
                @endif

                @if (! empty($payment->metadata))
                    <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5">
                        <h2 class="text-sm font-extrabold text-white font-display mb-3">Provider metadata</h2>
                        <dl class="space-y-2 text-xs">
                            @foreach ($payment->metadata as $key => $value)
                                <div class="flex justify-between gap-4">
                                    <dt class="text-slate-500 font-mono">{{ $key }}</dt>
                                    <dd class="text-slate-300 font-semibold text-right break-all">
                                        {{ is_scalar($value) ? $value : json_encode($value) }}
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif
            </div>

            <div class="space-y-5">
                <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5">
                    <h2 class="text-sm font-extrabold text-white font-display mb-3">Customer</h2>
                    @if ($payment->user)
                        <div class="flex items-center space-x-3">
                            <img src="{{ $payment->user->avatarUrl() }}" alt="{{ $payment->user->name }}" class="h-11 w-11 rounded-full object-cover ring-2 ring-slate-800 bg-slate-800 flex-shrink-0">
                            <div class="min-w-0">
                                <p class="font-bold text-white truncate">{{ $payment->user->name }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ $payment->user->email }}</p>
                            </div>
                        </div>
                        <dl class="mt-4 pt-4 border-t border-slate-800 space-y-2 text-[11px]">
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Phone</dt>
                                <dd class="text-slate-300 font-semibold font-mono">{{ $payment->user->phone_number ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Role</dt>
                                <dd class="text-slate-300 font-semibold">{{ ucfirst($payment->user->role ?? '—') }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-xs text-slate-500">This user has been deleted.</p>
                    @endif
                </div>

                <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5">
                    <h2 class="text-sm font-extrabold text-white font-display mb-3">Links</h2>
                    <div class="space-y-2">
                        @if ($payment->plan)
                            <div class="rounded-xl bg-slate-950/60 border border-slate-800 p-3">
                                <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Plan</p>
                                <p class="mt-0.5 text-xs font-bold text-white">{{ $payment->plan->name }}</p>
                                <a href="{{ route('admin.plans.edit', $payment->plan->id) }}" class="mt-1.5 inline-flex items-center gap-1 text-[11px] font-semibold text-amber-400 hover:text-amber-300">
                                    Edit plan
                                    <x-bi-arrow-left class="h-3 w-3 rotate-180" />
                                </a>
                            </div>
                        @endif

                        @if ($payment->subscription)
                            <div class="rounded-xl bg-slate-950/60 border border-slate-800 p-3">
                                <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Subscription</p>
                                <p class="mt-0.5 text-xs font-bold text-white">#{{ $payment->subscription->id }}</p>
                                <a href="{{ route('admin.subscriptions.show', $payment->subscription->id) }}" class="mt-1.5 inline-flex items-center gap-1 text-[11px] font-semibold text-amber-400 hover:text-amber-300">
                                    View subscription
                                    <x-bi-arrow-left class="h-3 w-3 rotate-180" />
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
