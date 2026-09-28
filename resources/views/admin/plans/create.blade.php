<x-admin-layout>
    <x-slot:title>New Plan — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-7xl mx-auto">
        <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <x-bi-arrow-left class="h-3.5 w-3.5" />
            Back to Plans
        </a>

        <div>
            <h1 class="text-2xl font-extrabold text-white font-display">Create a Subscription Plan</h1>
            <p class="text-xs text-slate-400 mt-0.5">Tiers are billed in Ugandan shillings and unlock access for the access window you set.</p>
        </div>

        <form method="POST" action="{{ route('admin.plans.store') }}">
            @csrf
            @include('admin.plans._form', ['plan' => null, 'submitLabel' => 'Create Plan'])
        </form>
    </div>
</x-admin-layout>
