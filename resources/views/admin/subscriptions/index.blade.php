<x-admin-layout>
    <x-slot:title>Subscriptions — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-7xl mx-auto">
        <div>
            <h1 class="text-2xl font-extrabold text-white font-display">
                Subscriptions
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">Manage subscriber access manually — useful for comps, refunds, and correcting provider callbacks.</p>
        </div>

        <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/60 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-3.5">Customer</th>
                            <th class="px-6 py-3.5">Plan</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Expires</th>
                            <th class="px-6 py-3.5">Auto-renew</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse ($subscriptions as $subscription)
                            @php
                                // Literal class names so Tailwind's JIT scanner can find them.
                                $statusClass = match ($subscription->status) {
                                    'active' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                    'pending' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                    'expired' => 'bg-slate-500/20 text-slate-400 border-slate-500/30',
                                    'cancelled' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                    default => 'bg-slate-500/20 text-slate-400 border-slate-500/30',
                                };
                            @endphp
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    @if ($subscription->user)
                                        <div class="flex items-center space-x-3">
                                            <img src="{{ $subscription->user->avatarUrl() }}" alt="{{ $subscription->user->name }}" class="h-9 w-9 rounded-full object-cover ring-2 ring-slate-800 bg-slate-800 flex-shrink-0">
                                            <div class="min-w-0">
                                                <span class="font-bold text-white block truncate">{{ $subscription->user->name }}</span>
                                                <span class="text-[11px] text-slate-400 font-mono">{{ $subscription->user->phone_number }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-500">Deleted user</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ $subscription->plan?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 rounded {{ $statusClass }} border px-2 py-0.5 text-[10px] font-bold">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                    @if ($subscription->isActive() && $subscription->daysRemaining() <= 3)
                                        <span class="block mt-1 text-[10px] font-semibold text-amber-400">
                                            {{ $subscription->daysRemaining() }}d left
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-400">
                                    {{ $subscription->expires_at?->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($subscription->auto_renew)
                                        <x-bi-check-lg class="h-3.5 w-3.5 text-emerald-400" />
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-colors">
                                        <x-bi-eye class="h-3 w-3" />
                                        Manage
                                    </a>
                                    <form method="POST" action="{{ route('admin.subscriptions.destroy', $subscription->id) }}" class="inline-block" onsubmit="return confirm('Delete this subscription record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-semibold transition-colors">
                                            <x-bi-trash class="h-3 w-3" />
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    No subscriptions yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($subscriptions->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
