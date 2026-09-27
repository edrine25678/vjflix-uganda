<x-admin-layout>
    <x-slot:title>Add Video Jockey — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-3xl mx-auto">
        <div>
            <a href="{{ route('admin.vjs.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-1">
                &larr; Back to VJs
            </a>
            <h1 class="text-2xl font-extrabold text-white font-display">
                Add Ugandan Video Jockey
            </h1>
        </div>

        <form method="POST" action="{{ route('admin.vjs.store') }}" enctype="multipart/form-data" class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
            @csrf

            <!-- Stage Name & Real Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Stage Name *</label>
                    <input type="text" name="stage_name" value="{{ old('stage_name') }}" required placeholder="e.g. VJ Junior" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Real Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Marysmarts Matovu" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
            </div>

            <!-- Specialization & Rating -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Specialization</label>
                    <input type="text" name="specialization" value="{{ old('specialization', 'Action, Sci-Fi & Tactical Blockbusters') }}" placeholder="e.g. Martial Arts & Crime" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Rating (1 to 5)</label>
                    <input type="number" step="0.01" min="1" max="5" name="rating" value="{{ old('rating', '4.85') }}" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
            </div>

            <!-- Biography -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Biography</label>
                <textarea name="biography" rows="4" placeholder="Background, career milestones, signature catchphrases..." class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">{{ old('biography') }}</textarea>
            </div>

            <!-- Photos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 rounded-xl bg-slate-950 border border-slate-800">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Profile Photo File</label>
                    <input type="file" name="avatar_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400">
                    <p class="text-[10px] text-slate-500 mt-1">Or provide photo URL below:</p>
                    <input type="url" name="avatar_url" value="{{ old('avatar_url') }}" placeholder="https://..." class="mt-1 w-full rounded-lg bg-slate-900 border border-slate-700 p-2 text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Cover Banner File</label>
                    <input type="file" name="cover_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400">
                    <p class="text-[10px] text-slate-500 mt-1">Or provide banner URL below:</p>
                    <input type="url" name="cover_url" value="{{ old('cover_url') }}" placeholder="https://..." class="mt-1 w-full rounded-lg bg-slate-900 border border-slate-700 p-2 text-xs text-white">
                </div>
            </div>

            <!-- Verification & Active Checkboxes -->
            <div class="flex items-center space-x-6 pt-4 border-t border-slate-800">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_verified" id="is_verified" value="1" {{ old('is_verified', true) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                    <label for="is_verified" class="text-xs font-bold text-slate-300">Verified Ugandan VJ</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                    <label for="is_active" class="text-xs font-bold text-slate-300">Active Status</label>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.vjs.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 text-xs font-bold text-slate-300 hover:bg-slate-700">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-lg shadow-amber-500/20 transition-all">
                    Save Video Jockey
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
