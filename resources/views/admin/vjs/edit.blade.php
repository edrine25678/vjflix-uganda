<x-admin-layout>
    <x-slot:title>Edit VJ: {{ $vj->stage_name }} — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.vjs.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-1">
                    &larr; Back to VJs
                </a>
                <h1 class="text-2xl font-extrabold text-white font-display">
                    Edit: {{ $vj->stage_name }}
                </h1>
            </div>
            <a href="/vjs/{{ $vj->slug }}" target="_blank" class="text-xs font-bold text-amber-400 hover:underline">
                View Profile &rarr;
            </a>
        </div>

        <form method="POST" action="{{ route('admin.vjs.update', $vj->id) }}" enctype="multipart/form-data" class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
            @csrf
            @method('PUT')

            <!-- Stage Name & Real Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Stage Name *</label>
                    <input type="text" name="stage_name" value="{{ old('stage_name', $vj->stage_name) }}" required class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Real Name</label>
                    <input type="text" name="name" value="{{ old('name', $vj->name) }}" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
            </div>

            <!-- Specialization & Rating -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Specialization</label>
                    <input type="text" name="specialization" value="{{ old('specialization', $vj->specialization) }}" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Rating (1 to 5)</label>
                    <input type="number" step="0.01" min="1" max="5" name="rating" value="{{ old('rating', $vj->rating) }}" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
            </div>

            <!-- Biography -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Biography</label>
                <textarea name="biography" rows="4" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">{{ old('biography', $vj->biography) }}</textarea>
            </div>

            <!-- Photos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 rounded-xl bg-slate-950 border border-slate-800">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Profile Photo File</label>
                    @if ($vj->profile_photo)
                        <img src="{{ $vj->avatarUrl() }}" alt="Avatar" class="h-16 w-16 rounded-full object-cover mb-2 border border-slate-700">
                    @endif
                    <input type="file" name="avatar_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400">
                    <input type="url" name="avatar_url" value="{{ old('avatar_url', $vj->profile_photo) }}" placeholder="Or paste photo URL" class="mt-2 w-full rounded-lg bg-slate-900 border border-slate-700 p-2 text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Cover Banner File</label>
                    @if ($vj->cover_photo)
                        <img src="{{ $vj->coverUrl() }}" alt="Cover" class="h-16 w-28 rounded object-cover mb-2 border border-slate-700">
                    @endif
                    <input type="file" name="cover_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400">
                    <input type="url" name="cover_url" value="{{ old('cover_url', $vj->cover_photo) }}" placeholder="Or paste banner URL" class="mt-2 w-full rounded-lg bg-slate-900 border border-slate-700 p-2 text-xs text-white">
                </div>
            </div>

            <!-- Verification & Active Checkboxes -->
            <div class="flex items-center space-x-6 pt-4 border-t border-slate-800">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_verified" id="is_verified" value="1" {{ old('is_verified', $vj->is_verified) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                    <label for="is_verified" class="text-xs font-bold text-slate-300">Verified Ugandan VJ</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $vj->is_active) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                    <label for="is_active" class="text-xs font-bold text-slate-300">Active Status</label>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.vjs.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 text-xs font-bold text-slate-300 hover:bg-slate-700">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-lg shadow-amber-500/20 transition-all">
                    Update Video Jockey
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
