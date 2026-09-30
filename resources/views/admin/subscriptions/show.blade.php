<x-admin-layout>
    <x-slot:title>Subscription #{{ $subscription->id }} — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-5xl mx-auto">
        <a href="{{ route('admin.subscriptions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <x-bi-arrow-left class="h-3.5 w-3.5" />
            Back to Subscriptions
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white font-display">Subscription #{{ $subscription->id }}</h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    Started {{ $subscription->starts_at?->format('M j, Y') ?? '—' }}
                    @if ($subscription->isActive())
                        · <span class="text-emerald-400 font-semibold">{{ $subscription->daysRemaining() }} days remaining</span>
                    @endif
                </p>
            </div>

            <form method="POST" action="{{ route('admin.subscriptions.destroy', $subscription->id) }}" onsubmit="return confirm('Delete this subscription record? The user will lose access immediately.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 self-start px-3.5 py-2 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-semibold transition-colors">
                    <x-bi-trash class="h-3.5 w-3.5" />
                    Delete
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 space-y-5">
                <form method="POST" action="{{ route('admin.subscriptions.update', $subscription->id) }}" class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <h2 class="text-sm font-extrabold text-white font-display">Access Settings</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-xs font-bold text-slate-300 mb-1.5">Status <span class="text-red-400">*</span></label>
                            <select name="status" id="status" required
                                    class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                @foreach (['active', 'pending', 'expired', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $subscription->status) === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-[10px] text-slate-500">Only <span class="text-emerald-400">active</span> subscriptions with a future expiry grant premium access.</p>
                        </div>

                        <div>
                            <label for="expires_at" class="block text-xs font-bold text-slate-300 mb-1.5">Expires at</label>
                            <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at', $subscription->expires_at?->format('Y-m-d')) }}"
                                   class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            <p class="mt-1 text-[10px] text-slate-500">Extend this to grant a comp or correct a provider callback.</p>
                        </div>
                    </div>

                    <div>
                        {{-- Paired hidden input so unchecking persists false, since the column defaults to false --}}
                        <input type="hidden" name="auto_renew" value="0">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="auto_renew" value="1" @checked(old('auto_renew', $subscription->auto_renew))
                                   class="h-4 w-4 rounded border-slate-600 bg-slate-950 text-amber-500 focus:ring-amber-500 focus:ring-offset-slate-900">
                            <span class="text-xs font-semibold text-slate-300">Auto-renew</span>
                        </label>
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-md shadow-amber-500/20 transition-all">
                            <x-bi-check-lg class="h-3.5 w-3.5" />
                            Save Changes
                        </button>
                    </div>
                </form>

                <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
                        <h2 class="text-sm font-extrabold text-white font-display">Payment history</h2>
                        <span class="rounded bg-slate-800 text-slate-400 border border-slate-700 px-2 py-0.5 text-[10px] font-bold">
                            {{ $subscription->payments->count() }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-950/60 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                                <tr>
                                    <th class="px-5 py-3">Reference</th>
                                    <th class="px-5 py-3">Amount</th>
                                    <th class="px-5 py-3">Method</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3 text-right">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80">
                                @forelse ($subscription->payments as $payment)
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="px-5 py-3 font-mono text-slate-200">{{ $payment->transaction_reference }}</td>
                                        <td class="px-5 py-3 font-bold text-white">{{ $payment->formattedAmount() }}</td>
                                        <td class="px-5 py-3">{{ $payment->networkLabel() }}</td>
                                        <td class="px-5 py-3">
                                            <span class="inline-flex items-center rounded px-2 py-0.5 text-[10px] font-bold {{ match ($payment->status) {
                                                'completed' => 'bg-emerald-500/20 text-emerald-400',
                                                'failed' => 'bg-red-500/20 text-red-400',
                                                'cancelled' => 'bg-slate-500/20 text-slate-400',
                                                default => 'bg-amber-500/20 text-amber-400',
                                            } }}">{{ ucfirst($payment->status) }}</span>
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-400 hover:text-amber-300">
                                                <x-bi-eye class="h-3 w-3" />
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-10 text-center text-slate-500">
                                            No payments linked to this subscription.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5">
                    <h2 class="text-sm font-extrabold text-white font-display mb-3">Customer</h2>
                    @if ($subscription->user)
                        <div class="flex items-center space-x-3">
                            <img src="{{ $subscription->user->avatarUrl() }}" alt="{{ $subscription->user->name }}" class="h-11 w-11 rounded-full object-cover ring-2 ring-slate-800 bg-slate-800 flex-shrink-0">
                            <div class="min-w-0">
                                <p class="font-bold text-white truncate">{{ $subscription->user->name }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ $subscription->user->email }}</p>
                            </div>
                        </div>
                        <dl class="mt-4 pt-4 border-t border-slate-800 space-y-2 text-[11px]">
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Phone</dt>
                                <dd class="text-slate-300 font-semibold font-mono">{{ $subscription->user->phone_number ?: '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Joined</dt>
                                <dd class="text-slate-300 font-semibold">{{ $subscription->user->created_at->format('M j, Y') }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-xs text-slate-500">This user has been deleted.</p>
                    @endif
                </div>

                <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5">
                    <h2 class="text-sm font-extrabold text-white font-display mb-3">Plan</h2>
                    @if ($subscription->plan)
                        <p class="font-bold text-white">{{ $subscription->plan->name }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ $subscription->plan->durationLabel() }} access</p>
                        <p class="mt-1.5 text-xs font-bold text-amber-400">{{ $subscription->plan->formattedPrice() }}</p>
                        <a href="{{ route('admin.plans.edit', $subscription->plan->id) }}" class="mt-2.5 inline-flex items-center gap-1 text-[11px] font-semibold text-amber-400 hover:text-amber-300">
                            Edit plan
                            <x-bi-arrow-left class="h-3 w-3 rotate-180" />
                        </a>
                    @else
                        <p class="text-xs text-slate-500">The plan this was bought under has been deleted.</p>
                    @endif

                    <dl class="mt-4 pt-4 border-t border-slate-800 space-y-2 text-[11px]">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Started</dt>
                            <dd class="text-slate-300 font-semibold">{{ $subscription->starts_at?->format('M j, Y') ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Expires</dt>
                            <dd class="text-slate-300 font-semibold">{{ $subscription->expires_at?->format('M j, Y') ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Method</dt>
                            <dd class="text-slate-300 font-semibold">{{ ucfirst(str_replace('_', ' ', $subscription->payment_method ?? '—')) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
