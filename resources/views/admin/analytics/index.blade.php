<x-admin-layout>
    <x-slot:title>Analytics — VJFlix CMS</x-slot:title>

    <div class="space-y-8 max-w-7xl mx-auto">
        <!-- Header & Period Selector -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white font-display">
                    Analytics
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Platform totals are all-time; the daily series below respect the selected window.</p>
            </div>

            <div class="flex items-center gap-1 rounded-xl bg-slate-900 border border-slate-800 p-1">
                @foreach ([7 => '7d', 30 => '30d', 90 => '90d', 365 => '1y'] as $days => $label)
                    <a href="{{ route('admin.analytics.index', ['period' => $days]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ (int) $period === $days ? 'bg-amber-500 text-black' : 'text-slate-400 hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Platform Totals -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach ([
                ['Total users', number_format($platformStats['total_users']), $platformStats['active_users'].' active', 'people'],
                ['Total views', number_format($platformStats['total_views']), 'All content', 'eye'],
                ['Watch time', number_format($platformStats['total_watch_time']), 'Minutes', 'clock'],
                ['Revenue', 'UGX '.number_format($platformStats['total_revenue']), 'Completed payments', 'cash-stack'],
                ['Active subs', number_format($platformStats['active_subscriptions']), 'Currently valid', 'calendar-check'],
                ['Catalog', $platformStats['total_movies'].'M / '.$platformStats['total_series'].'S', $platformStats['total_vjs'].' VJs', 'film'],
            ] as [$label, $value, $hint, $icon])
                <div class="rounded-2xl bg-slate-900 border border-slate-800 p-4">
                    <span class="flex items-center gap-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        @if ($icon === 'people')
                            <x-bi-people class="h-3 w-3 text-amber-400" />
                        @elseif ($icon === 'eye')
                            <x-bi-eye class="h-3 w-3 text-amber-400" />
                        @elseif ($icon === 'clock')
                            <x-bi-clock class="h-3 w-3 text-amber-400" />
                        @elseif ($icon === 'cash-stack')
                            <x-bi-cash-stack class="h-3 w-3 text-amber-400" />
                        @elseif ($icon === 'calendar-check')
                            <x-bi-calendar-check class="h-3 w-3 text-amber-400" />
                        @else
                            <x-bi-film class="h-3 w-3 text-amber-400" />
                        @endif
                        {{ $label }}
                    </span>
                    <span class="text-xl sm:text-2xl font-extrabold text-white mt-1.5 block font-display truncate">{{ $value }}</span>
                    <span class="text-[10px] text-amber-400 mt-1 block">{{ $hint }}</span>
                </div>
            @endforeach
        </div>

        <!-- Daily Series (no chart lib in this project, so a bar list) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ([
                ['Daily active users', $dailyActiveUsers, 'count', 'Unique viewers with watch progress'],
                ['Daily watch time', $dailyWatchTime, 'total_minutes', 'Minutes watched'],
            ] as [$heading, $rows, $valueKey, $hint])
                @php
                    $values = array_map(fn ($row) => (float) ($row->$valueKey ?? 0), $rows);
                    $peak = $values ? max($values) : 0;
                @endphp
                <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4">
                    <div>
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                            {{ $heading }}
                        </h2>
                        <p class="text-[11px] text-slate-400 mt-1">{{ $hint }} · last {{ (int) $period }} days</p>
                    </div>

                    @if (! empty($rows))
                        <div class="space-y-1.5 max-h-72 overflow-y-auto pr-1">
                            @foreach ($rows as $row)
                                @php
                                    $value = (float) ($row->$valueKey ?? 0);
                                    $width = $peak > 0 ? max(2, round(($value / $peak) * 100)) : 0;
                                @endphp
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] text-slate-500 font-mono w-16 flex-shrink-0">{{ \Illuminate\Support\Carbon::parse($row->date)->format('M j') }}</span>
                                    <div class="flex-1 h-5 rounded bg-slate-950/60 overflow-hidden">
                                        <div class="h-full rounded bg-amber-500/70" style="width: {{ $width }}%"></div>
                                    </div>
                                    <span class="text-[11px] text-slate-300 font-bold w-16 text-right flex-shrink-0">
                                        {{ $valueKey === 'total_minutes' ? number_format($value).'m' : number_format($value) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="py-8 text-center text-xs text-slate-500">No watch activity in this window.</p>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Audience & Monetisation -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                    Audience
                </h2>

                <dl class="space-y-3 text-xs">
                    @foreach ([
                        ['Total users', number_format($userAnalytics['total_users'])],
                        ['Active users', number_format($userAnalytics['active_users'])],
                        ['New this month', number_format($userAnalytics['new_users_this_month'])],
                        ['Subscribed users', number_format($userAnalytics['users_with_subscriptions'])],
                    ] as [$label, $value])
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-400">{{ $label }}</dt>
                            <dd class="text-white font-bold font-display text-lg">{{ $value }}</dd>
                        </div>
                    @endforeach

                    <div class="pt-3 border-t border-slate-800">
                        <div class="flex items-center justify-between mb-1.5">
                            <dt class="text-slate-400">Conversion rate</dt>
                            <dd class="text-amber-400 font-bold font-display text-lg">{{ $userAnalytics['subscription_rate'] }}%</dd>
                        </div>
                        <div class="h-2 rounded-full bg-slate-950 overflow-hidden">
                            <div class="h-full rounded-full bg-amber-500" style="width: {{ min(100, $userAnalytics['subscription_rate']) }}%"></div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-800">
                        <div class="flex items-center justify-between mb-1.5">
                            <dt class="text-slate-400">Completion rate</dt>
                            <dd class="text-emerald-400 font-bold font-display text-lg">{{ $completionRate['completion_rate'] }}%</dd>
                        </div>
                        <div class="h-2 rounded-full bg-slate-950 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ min(100, $completionRate['completion_rate']) }}%"></div>
                        </div>
                        <p class="mt-1.5 text-[10px] text-slate-500">
                            {{ $completionRate['completed'] }} of {{ $completionRate['total_watched'] }} started titles finished
                        </p>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                    Monetisation
                </h2>

                <dl class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Active subscriptions</dt>
                        <dd class="text-white font-bold font-display text-lg">{{ number_format($subscriptionAnalytics['active_subscriptions']) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">All subscriptions</dt>
                        <dd class="text-white font-bold font-display text-lg">{{ number_format($subscriptionAnalytics['total_subscriptions']) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Revenue this month</dt>
                        <dd class="text-amber-400 font-bold font-display text-lg">UGX {{ number_format($subscriptionAnalytics['revenue_this_month']) }}</dd>
                    </div>
                </dl>

                <div class="pt-4 border-t border-slate-800">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2.5">Active subs by plan</p>
                    @forelse ($subscriptionAnalytics['subscriptions_by_plan'] as $row)
                        <div class="flex items-center justify-between py-1.5 text-xs">
                            <span class="text-slate-300">{{ $row->name }}</span>
                            <span class="text-white font-bold font-display">{{ $row->count }}</span>
                        </div>
                    @empty
                        <p class="py-3 text-center text-xs text-slate-500">No active subscriptions.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Content by Watch Time -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ([
                ['Top movies by watch time', $topContentByWatchTime['movies']],
                ['Top series by watch time', $topContentByWatchTime['series']],
            ] as [$heading, $rows])
                <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                        {{ $heading }}
                    </h2>

                    <div class="divide-y divide-slate-800">
                        @forelse ($rows as $row)
                            @php
                                $watchMinutes = (int) round((float) $row->total_minutes);
                            @endphp
                            <div class="py-2.5 flex items-center justify-between gap-4">
                                <span class="text-xs font-semibold text-white truncate">{{ $row->title }}</span>
                                <span class="text-[11px] text-amber-400 font-bold flex-shrink-0">
                                    @if ($watchMinutes >= 60)
                                        {{ intdiv($watchMinutes, 60) }}h {{ $watchMinutes % 60 }}m
                                    @else
                                        {{ $watchMinutes }}m
                                    @endif
                                </span>
                            </div>
                        @empty
                            <p class="py-4 text-xs text-slate-500">No watch data yet.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Genre, Movie and VJ leaderboards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-3">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                    Top genres
                </h2>
                <div class="divide-y divide-slate-800">
                    @forelse ($genreStats as $genre)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <span class="text-slate-300 truncate">{{ $genre->name }}</span>
                            <span class="text-white font-bold font-display">{{ $genre->count }}</span>
                        </div>
                    @empty
                        <p class="py-4 text-xs text-slate-500">No genres in use.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-3">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                    Most viewed movies
                </h2>
                <div class="divide-y divide-slate-800">
                    @forelse ($movieStats as $movie)
                        <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <span class="text-slate-300 truncate block">{{ $movie['title'] }}</span>
                                <span class="text-[10px] text-slate-500">{{ $movie['year'] }}</span>
                            </div>
                            <span class="text-white font-bold font-display flex-shrink-0">{{ number_format($movie['views']) }}</span>
                        </div>
                    @empty
                        <p class="py-4 text-xs text-slate-500">No published movies.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-3">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                    Busiest VJs
                </h2>
                <div class="divide-y divide-slate-800">
                    @forelse ($vjStats as $vj)
                        <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                            <span class="text-slate-300 truncate">{{ $vj['stage_name'] }}</span>
                            <span class="text-white font-bold font-display flex-shrink-0">{{ $vj['total_content'] }}</span>
                        </div>
                    @empty
                        <p class="py-4 text-xs text-slate-500">No active VJs.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
