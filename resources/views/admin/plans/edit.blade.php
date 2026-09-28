<x-admin-layout>
    <x-slot:title>Edit {{ $plan->name }} — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-7xl mx-auto">
        <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <x-bi-arrow-left class="h-3.5 w-3.5" />
            Back to Plans
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white font-display">Edit {{ $plan->name }}</h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    <span class="font-mono text-slate-300">{{ $plan->slug }}</span> ·
                    {{ $plan->subscriptions()->count() }} subscriber{{ $plan->subscriptions()->count() === 1 ? '' : 's' }} on this plan
                </p>
            </div>

            <form method="POST" action="{{ route('admin.plans.destroy', $plan->id) }}" onsubmit="return confirm('Delete the {{ $plan->name }} plan? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-semibold transition-colors">
                    <x-bi-trash class="h-3.5 w-3.5" />
                    Delete plan
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.plans.update', $plan->id) }}">
            @csrf
            @method('PUT')
            @include('admin.plans._form', ['plan' => $plan, 'submitLabel' => 'Save Changes'])
        </form>
    </div>
</x-admin-layout>
