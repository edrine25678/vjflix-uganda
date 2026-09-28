<x-admin-layout>
    <x-slot:title>Subscription Plans — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white font-display">
                    Subscription Plans
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Pricing tiers shown to viewers on the public plans page. Order is controlled by <span class="font-mono text-slate-300">sort_order</span>.</p>
            </div>

            <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center gap-1.5 self-start px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-md shadow-amber-500/20 transition-all">
                <x-bi-plus-lg class="h-3.5 w-3.5 stroke-[2]" />
                New Plan
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
            @forelse ($plans as $plan)
                <div class="flex flex-col rounded-2xl bg-slate-900 border {{ $plan->is_active ? 'border-slate-800' : 'border-slate-800/60 opacity-60' }} shadow-xl overflow-hidden">
                    @if ($plan->badge)
                        <div class="bg-amber-500 px-4 py-1.5 text-center text-[10px] font-extrabold uppercase tracking-widest text-black">
                            {{ $plan->badge }}
                        </div>
                    @endif

                    <div class="flex-1 p-5 flex flex-col">
                        <div class="flex items-start justify-between gap-2">
                            <h2 class="text-lg font-extrabold text-white font-display">{{ $plan->name }}</h2>
                            @if (! $plan->is_active)
                                <span class="rounded bg-slate-800 text-slate-400 border border-slate-700 px-2 py-0.5 text-[10px] font-bold">Hidden</span>
                            @endif
                        </div>

                        <div class="mt-3">
                            <span class="text-3xl font-extrabold text-amber-400 font-display">{{ $plan->formattedPrice() }}</span>
                            <span class="block text-[11px] text-slate-400 mt-0.5">every {{ $plan->durationLabel() }}</span>
                        </div>

                        @if ($plan->description)
                            <p class="mt-3 text-xs text-slate-400 leading-relaxed">{{ $plan->description }}</p>
                        @endif

                        @if (! empty($plan->features))
                            <ul class="mt-4 space-y-2 text-xs text-slate-300 flex-1">
                                @foreach ($plan->features as $feature)
                                    <li class="flex items-start gap-2">
                                        <x-bi-check-lg class="h-3.5 w-3.5 mt-0.5 flex-shrink-0 text-amber-400" />
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <dl class="mt-5 pt-4 border-t border-slate-800 grid grid-cols-2 gap-y-2 text-[11px]">
                            <dt class="text-slate-500">Duration</dt>
                            <dd class="text-slate-300 text-right font-semibold">{{ $plan->duration_days }} days</dd>
                            <dt class="text-slate-500">Subscribers</dt>
                            <dd class="text-slate-300 text-right font-semibold">{{ $plan->subscriptions()->count() }}</dd>
                            <dt class="text-slate-500">Sort order</dt>
                            <dd class="text-slate-300 text-right font-semibold font-mono">{{ $plan->sort_order }}</dd>
                        </dl>
                    </div>

                    <div class="flex items-center gap-2 px-5 py-3.5 bg-slate-950/40 border-t border-slate-800">
                        <a href="{{ route('admin.plans.edit', $plan->id) }}" class="inline-flex items-center gap-1.5 flex-1 justify-center px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-colors">
                            <x-bi-pencil class="h-3 w-3" />
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.plans.destroy', $plan->id) }}" onsubmit="return confirm('Delete the {{ $plan->name }} plan? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-semibold transition-colors">
                                <x-bi-trash class="h-3 w-3" />
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl bg-slate-900 border border-slate-800 py-16 text-center">
                    <x-bi-list-check class="h-8 w-8 mx-auto text-slate-600" />
                    <p class="mt-3 text-sm font-semibold text-slate-400">No plans yet.</p>
                    <a href="{{ route('admin.plans.create') }}" class="mt-2 inline-block text-xs font-bold text-amber-400 hover:text-amber-300">Create the first plan</a>
                </div>
            @endforelse
        </div>
    </div>
</x-admin-layout>
