@php
    $totals = $dashboard_stats['totals'];
    $registration_status = $evento->getRegistrationStatus();
    $status_tones = [
        'success' => 'bg-emerald-100 text-emerald-800',
        'warning' => 'bg-amber-100 text-amber-800',
        'danger' => 'bg-red-100 text-red-800',
        'default' => 'bg-gray-100 text-gray-700',
    ];
    $statusClass = $status_tones[$registration_status['level']] ?? $status_tones['default'];
    $resumeUrl = url('/evento/dashboard/' . $evento->id . '?tab=resume');
@endphp

<div class="mb-6 overflow-hidden rounded-xl border border-purple-100 bg-white shadow-sm">
    <div class="border-t-4 border-brand p-4 sm:p-5">
        <div class="grid gap-4 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-4">
                <p class="mb-2 text-sm text-gray-600">
                    <span class="font-semibold text-gray-800">Período:</span>
                    {{ $evento->getDataInicio() ?: '—' }}
                    <span class="mx-1 text-gray-400">→</span>
                    {{ $evento->getDataFim() ?: '—' }}
                </p>
                <p class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-800">Inscrições:</span>
                    <span class="ml-1 inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClass }}">
                        {{ $registration_status['label'] }}
                    </span>
                </p>
            </div>

            <div class="border-purple-100 lg:col-span-5 lg:border-x lg:px-5">
                <div class="flex flex-wrap items-end gap-x-4 gap-y-2">
                    @foreach([
                        ['value' => $totals['inscritos'], 'label' => 'Inscritos'],
                        ['value' => $totals['confirmados'], 'label' => 'Confirmados'],
                        ['value' => $totals['presentes'], 'label' => 'Presentes'],
                    ] as $kpi)
                        <div class="min-w-[4.5rem]">
                            <p class="text-2xl font-bold text-brand-dark">{{ $kpi['value'] }}</p>
                            <p class="text-[0.65rem] font-medium uppercase tracking-wide text-gray-500">{{ $kpi['label'] }}</p>
                        </div>
                    @endforeach
                </div>

                @if($dashboard_stats['limits'])
                    @php $limits = $dashboard_stats['limits']; @endphp
                    <div class="mt-3">
                        <p class="text-xs text-gray-500">
                            Limite: {{ $limits['total'] }}/{{ $limits['limit'] }} ({{ $limits['pct'] }}%)
                        </p>
                        <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-gray-200">
                            <div class="h-full rounded-full {{ $limits['pct'] >= 100 ? 'bg-red-500' : ($limits['pct'] >= 85 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                 style="width: {{ min(100, $limits['pct']) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-3 lg:text-right">
                @include('components.v2.btn', [
                    'href' => $resumeUrl,
                    'label' => 'Resumo completo',
                    'variant' => 'secondary',
                    'size' => 'sm',
                ])
            </div>
        </div>

        @include('evento.v2._partials.dashboard_alerts', [
            'compact' => true,
            'alert_collapse_id' => 'strip',
        ])
    </div>
</div>
