@php
    $editing = isset($plan) && $plan;
    $seedFeatures = $editing && ! empty($plan->features) ? $plan->features : [''];
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5 space-y-5">
                <h2 class="text-sm font-extrabold text-white font-display">Plan Details</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-300 mb-1.5">Name <span class="text-red-400">*</span></label>
                        <input type="text" name="name" id="name" required maxlength="255" value="{{ old('name', $editing ? $plan->name : '') }}" placeholder="e.g. Monthly Premium"
                               class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label for="slug" class="block text-xs font-bold text-slate-300 mb-1.5">Slug</label>
                        <input type="text" name="slug" id="slug" maxlength="255" value="{{ old('slug', $editing ? $plan->slug : '') }}" placeholder="Auto-generated from name"
                               class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <p class="mt-1 text-[10px] text-slate-500">Leave blank to derive it from the name.</p>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-slate-300 mb-1.5">Description</label>
                    <textarea name="description" id="description" rows="3" placeholder="Short marketing blurb shown on the plans page"
                              class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('description', $editing ? $plan->description : '') }}</textarea>
                </div>

                <div>
                    <label for="badge" class="block text-xs font-bold text-slate-300 mb-1.5">Badge</label>
                    <input type="text" name="badge" id="badge" maxlength="50" value="{{ old('badge', $editing ? $plan->badge : '') }}" placeholder="e.g. Most Popular"
                           class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5 space-y-4">
                <h2 class="text-sm font-extrabold text-white font-display">Pricing &amp; Billing Cycle</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="price_ugx" class="block text-xs font-bold text-slate-300 mb-1.5">Price (UGX) <span class="text-red-400">*</span></label>
                        <input type="number" name="price_ugx" id="price_ugx" required min="0" step="500" value="{{ old('price_ugx', $editing ? $plan->price_ugx : '') }}" placeholder="15000"
                               class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label for="interval_count" class="block text-xs font-bold text-slate-300 mb-1.5">Every N <span class="text-red-400">*</span></label>
                        <input type="number" name="interval_count" id="interval_count" required min="1" value="{{ old('interval_count', $editing ? $plan->interval_count : 1) }}"
                               class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label for="interval_unit" class="block text-xs font-bold text-slate-300 mb-1.5">Unit <span class="text-red-400">*</span></label>
                        <select name="interval_unit" id="interval_unit" required
                                class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @foreach (['day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('interval_unit', $editing ? $plan->interval_unit : 'month') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="duration_days" class="block text-xs font-bold text-slate-300 mb-1.5">Access window (days) <span class="text-red-400">*</span></label>
                    <input type="number" name="duration_days" id="duration_days" required min="1" value="{{ old('duration_days', $editing ? $plan->duration_days : 30) }}"
                           class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 sm:max-w-xs">
                    <p class="mt-1 text-[10px] text-slate-500">How long a single payment unlocks access. This is what <span class="font-mono">subscriptions.expires_at</span> is set from, so it does not have to match the billing cycle.</p>
                </div>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5 space-y-4"
                 x-data="{ features: @js($seedFeatures) }">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-extrabold text-white font-display">Included Features</h2>
                    <button type="button" @click="features.push('')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-colors">
                        <x-bi-plus-lg class="h-3 w-3" />
                        Add row
                    </button>
                </div>

                <p class="text-[10px] text-slate-500">Each row is saved as one entry in the <span class="font-mono">features</span> JSON column. Blank rows are ignored.</p>

                <div class="space-y-2">
                    <template x-for="(feature, i) in features" :key="i">
                        <div class="flex items-center gap-2">
                            <input type="text" name="features[]" x-model="features[i]" placeholder="e.g. Ad-free streaming"
                                   class="flex-1 rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            <button type="button" @click="features.splice(i, 1)" title="Remove row"
                                    class="flex-shrink-0 p-2 rounded-lg bg-slate-800 hover:bg-red-500/20 text-slate-400 hover:text-red-400 transition-colors">
                                <x-bi-x-lg class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="features.push('')" x-show="features.length === 0"
                        class="w-full py-2.5 rounded-xl border border-dashed border-slate-700 text-slate-500 text-xs font-semibold hover:border-amber-500 hover:text-amber-400 transition-colors">
                    No features yet — add the first one
                </button>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5 space-y-4">
                <h2 class="text-sm font-extrabold text-white font-display">Visibility</h2>

                <div>
                    <label for="sort_order" class="block text-xs font-bold text-slate-300 mb-1.5">Sort order</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $editing ? $plan->sort_order : 0) }}"
                           class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <p class="mt-1 text-[10px] text-slate-500">Lower numbers show first. Plans are listed in ascending order.</p>
                </div>

                <div class="pt-1">
                    {{-- Paired hidden input so unchecking actually persists false, since the column defaults to true --}}
                    <input type="hidden" name="is_active" value="0">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editing ? $plan->is_active : true))
                               class="h-4 w-4 rounded border-slate-600 bg-slate-950 text-amber-500 focus:ring-amber-500 focus:ring-offset-slate-900">
                        <span class="text-xs font-semibold text-slate-300">Active</span>
                    </label>
                    <p class="mt-1.5 text-[10px] text-slate-500">Inactive plans are hidden from the public plans page and cannot be subscribed to.</p>
                </div>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl p-5">
                <h2 class="text-sm font-extrabold text-white font-display mb-3">Summary</h2>
                <dl class="space-y-2 text-[11px]">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Price</dt>
                        <dd class="text-slate-300 font-semibold font-mono">UGX {{ number_format((int) old('price_ugx', $editing ? $plan->price_ugx : 0)) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Access window</dt>
                        <dd class="text-slate-300 font-semibold font-mono">{{ (int) old('duration_days', $editing ? $plan->duration_days : 30) }} days</dd>
                    </div>
                    @if ($editing)
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Existing subscribers</dt>
                            <dd class="text-slate-300 font-semibold font-mono">{{ $plan->subscriptions()->count() }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="flex flex-col gap-2">
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 w-full px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-md shadow-amber-500/20 transition-all">
                    <x-bi-check-lg class="h-3.5 w-3.5" />
                    {{ $submitLabel }}
                </button>

                <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-colors">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</div>
