@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan rekap pendukung per TPS')

@section('content')
@php
    $mainCandidate = $candidates->firstWhere('is_main_candidate', true) ?? $candidates->first();
    $hasQuickData  = $quickSuaraMasuk > 0;
@endphp
<div class="py-4 space-y-6">

    {{-- ===== SUMMARY CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

        {{-- Card: Total TPS --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-2xl bg-sky-100/80 text-sky-700 flex items-center justify-center flex-shrink-0 shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalTps) }}</p>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mt-1">Total TPS Terdaftar</p>
            </div>
        </div>

        {{-- Card: Total Pemilih --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-2xl bg-indigo-100/80 text-indigo-700 flex items-center justify-center flex-shrink-0 shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalVoters) }}</p>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mt-1">Total Pemilih (DPT)</p>
            </div>
        </div>

        {{-- Card: Total Pendukung --}}
        <div class="bg-gradient-to-tr from-emerald-600 to-teal-700 text-white rounded-3xl p-6 shadow-lg shadow-emerald-950/20 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-white/20 text-white flex items-center justify-center flex-shrink-0 backdrop-blur-md">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-3xl font-extrabold tracking-tight">{{ number_format($totalSupporters) }}</p>
                <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mt-1">Total Pendukung</p>
                @if($totalVoters > 0)
                <p class="text-emerald-200 text-xs font-extrabold mt-1 bg-white/10 px-2.5 py-0.5 rounded-md inline-block">
                    {{ number_format(($totalSupporters / $totalVoters) * 100, 1) }}% dari DPT
                </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== QUICK COUNT PIE CHART + STATS ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- Pie Chart Card --}}
        <div class="lg:col-span-3 bg-white rounded-3xl p-6 lg:p-8 border border-slate-200/80 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 text-[10px] font-extrabold text-indigo-600 bg-indigo-50 border border-indigo-200 px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Live Quick Count
                    </div>
                    <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Perolehan Suara Quick Count</h2>
                    <p class="text-slate-500 text-xs mt-0.5">
                        Distribusi suara sementara dari
                        <span class="font-bold text-indigo-600">{{ $totalQuickSubmitted }} dari {{ $totalTps }} TPS</span>
                        yang sudah menginput data
                    </p>
                </div>
                <a href="{{ route('rekap-quick.index') }}" class="flex-shrink-0 inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold px-3.5 py-2 rounded-xl transition-colors shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Rekap Lengkap
                </a>
            </div>

            @if(!$hasQuickData)
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-indigo-50 flex items-center justify-center mb-5">
                        <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                    </div>
                    <p class="text-slate-700 font-extrabold text-sm">Belum Ada Data Quick Count</p>
                    <p class="text-slate-400 text-xs mt-1.5 max-w-xs">Saksi TPS belum menginput perolehan suara sementara. Data akan tampil otomatis setelah ada input.</p>
                    <a href="{{ route('quickcount.index') }}" class="mt-5 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs px-5 py-2.5 rounded-xl transition-colors shadow-sm">
                        Input Quick Count Sekarang
                    </a>
                </div>
            @else
                {{-- PIE CHART --}}
                <div class="flex flex-col sm:flex-row items-center gap-8">
                    {{-- Chart Canvas --}}
                    <div class="relative flex-shrink-0" style="width: 220px; height: 220px;">
                        <canvas id="quickPieChart"></canvas>
                        {{-- Centre label --}}
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Suara Masuk</p>
                            <p class="text-2xl font-extrabold text-slate-900 leading-tight">{{ number_format($quickSuaraMasuk) }}</p>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5">suara</p>
                        </div>
                    </div>

                    {{-- Legend & Stats --}}
                    <div class="flex-1 w-full space-y-3">
                        @foreach($candidates as $idx => $candidate)
                        @php
                            $suaraCand = $quickPieData[$idx] ?? 0;
                            $pct = $quickSuaraMasuk > 0 ? round(($suaraCand / $quickSuaraMasuk) * 100, 1) : 0;
                            $color = $quickPieColors[$idx] ?? '#6366f1';
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $color }}"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1 gap-2">
                                    <p class="text-xs font-extrabold text-slate-800 truncate">No. {{ $candidate->nomor_urut }} — {{ $candidate->nama }}</p>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="text-xs font-black text-slate-900">{{ number_format($suaraCand) }}</span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md text-white" style="background-color: {{ $color }}">{{ $pct }}%</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full transition-all duration-700" style="width: {{ min(100, $pct) }}%; background-color: {{ $color }}"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        {{-- Total Suara Sah --}}
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <p class="text-xs font-bold text-slate-500">Total Suara Sah</p>
                            <p class="text-sm font-extrabold text-slate-900">{{ number_format($quickSuaraSah) }} <span class="text-slate-400 font-medium text-xs">suara</span></p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Quick Count Stats Panel --}}
        <div class="lg:col-span-2 flex flex-col gap-5">

            {{-- Card: TPS Sudah Input Quick Count --}}
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 text-white rounded-3xl p-6 shadow-lg shadow-indigo-950/20 flex-1 flex flex-col justify-between min-h-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0 text-xl backdrop-blur-md">⚡</div>
                    <div>
                        <p class="text-indigo-100 text-[11px] font-extrabold uppercase tracking-wider">TPS Input Quick Count</p>
                        <p class="text-3xl font-extrabold tracking-tight mt-0.5">{{ $totalQuickSubmitted }}<span class="text-indigo-300 text-lg font-bold"> / {{ $totalTps }}</span></p>
                    </div>
                </div>
                {{-- Progress Bar --}}
                <div class="mt-5">
                    @php $pctTps = $totalTps > 0 ? round(($totalQuickSubmitted / $totalTps) * 100) : 0; @endphp
                    <div class="flex items-center justify-between mb-1.5">
                        <p class="text-indigo-200 text-xs font-bold">Progress Input</p>
                        <p class="text-white text-xs font-extrabold">{{ $pctTps }}%</p>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full bg-white transition-all duration-700" style="width: {{ $pctTps }}%"></div>
                    </div>
                    <p class="text-indigo-200 text-[10px] mt-1.5">{{ $totalTps - $totalQuickSubmitted }} TPS belum input</p>
                </div>
            </div>

            {{-- Card: Unggul Quick Count --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex-1">
                @if($hasQuickData && $mainCandidate)
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Unggul Quick Count</p>
                <p class="text-xl font-extrabold text-slate-900 leading-tight truncate">{{ Str::words($mainCandidate->nama, 2) }}</p>
                <div class="mt-3 flex items-end gap-2">
                    <p class="text-3xl font-extrabold tracking-tight" style="color: {{ $mainCandidate->warna_badge ?? '#6366f1' }}">
                        {{ $quickPersentaseKandidat }}%
                    </p>
                    <p class="text-slate-400 text-xs font-bold mb-1">dari suara sah</p>
                </div>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-700" style="width: {{ min(100, $quickPersentaseKandidat) }}%; background-color: {{ $mainCandidate->warna_badge ?? '#6366f1' }}"></div>
                </div>
                <p class="text-slate-500 text-xs font-semibold mt-2">{{ number_format($quickSuaraKandidat) }} dari {{ number_format($quickSuaraSah) }} suara sah</p>
                @else
                <div class="flex flex-col items-center justify-center h-full py-6 text-center">
                    <p class="text-3xl mb-2">🗳️</p>
                    <p class="text-sm font-extrabold text-slate-700">Menunggu Data Quick Count</p>
                    <p class="text-xs text-slate-400 mt-1">Akan tampil otomatis setelah ada input dari saksi TPS</p>
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ===== BAR CHART CONTAINER ===== --}}
    <div class="bg-white rounded-3xl p-6 lg:p-8 border border-slate-200/80 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Perbandingan Pemilih vs Pendukung per TPS</h2>
                <p class="text-slate-500 text-xs mt-0.5">Visualisasi grafik perbandingan data pemilih di setiap Tempat Pemungutan Suara</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-bold self-start sm:self-auto bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200/60">
                <span class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-sky-500 inline-block shadow-sm"></span>
                    Total Pemilih
                </span>
                <span class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block shadow-sm"></span>
                    Pendukung
                </span>
            </div>
        </div>

        @if($chartLabels->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-slate-300">
                <svg class="w-16 h-16 mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="text-slate-600 font-extrabold">Belum Ada Data TPS</p>
                <a href="{{ route('tps.create') }}" class="mt-4 px-5 py-2.5 bg-emerald-600 text-white font-bold rounded-xl text-xs shadow-md">Tambah TPS Sekarang</a>
            </div>
        @else
            <div class="relative w-full overflow-x-auto">
                <div class="min-w-[500px]" style="height: 380px;">
                    <canvas id="tpsChart"></canvas>
                </div>
            </div>
        @endif
    </div>

    {{-- ===== QUICK ACCESS CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <a href="{{ route('tps.create') }}" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md hover:border-sky-300 transition-all duration-200 group">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 group-hover:bg-sky-100 text-sky-600 flex items-center justify-center transition-colors shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <p class="font-extrabold text-slate-900 text-sm group-hover:text-sky-600 transition-colors">Tambah TPS Baru</p>
                <p class="text-slate-400 text-xs mt-0.5">Daftarkan Tempat Pemungutan Suara baru</p>
            </div>
        </a>

        <a href="{{ route('voters.create') }}" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md hover:border-emerald-300 transition-all duration-200 group">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 text-emerald-600 flex items-center justify-center transition-colors shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <div>
                <p class="font-extrabold text-slate-900 text-sm group-hover:text-emerald-600 transition-colors">Tambah Pemilih</p>
                <p class="text-slate-400 text-xs mt-0.5">Input data pemilih secara manual</p>
            </div>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ===== PIE CHART QUICK COUNT =====
    @if($hasQuickData)
    (function () {
        const pieCtx = document.getElementById('quickPieChart');
        if (!pieCtx) return;

        const labels = @json($quickPieLabels);
        const data   = @json($quickPieData);
        const colors = @json($quickPieColors);

        // Slightly transparent versions for hover
        const hoverColors = colors.map(c => c + 'cc');

        new Chart(pieCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    hoverBackgroundColor: hoverColors,
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverBorderWidth: 4,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                animation: {
                    animateRotate: true,
                    animateScale: false,
                    duration: 900,
                    easing: 'easeOutQuart',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#94a3b8',
                        padding: 14,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct   = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                return ` ${ctx.parsed.toLocaleString('id-ID')} suara (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    })();
    @endif

    // ===== BAR CHART TPS =====
    @if(!$chartLabels->isEmpty())
    (function () {
        const ctx        = document.getElementById('tpsChart').getContext('2d');
        const labels     = @json($chartLabels);
        const voters     = @json($chartVoters);
        const supporters = @json($chartSupporters);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Pemilih',
                        data: voters,
                        backgroundColor: 'rgba(56, 189, 248, 0.85)',
                        borderColor: 'rgba(2, 132, 199, 1)',
                        borderWidth: 2,
                        borderRadius: 10,
                        borderSkipped: false,
                    },
                    {
                        label: 'Pendukung',
                        data: supporters,
                        backgroundColor: 'rgba(16, 185, 129, 0.9)',
                        borderColor: 'rgba(5, 150, 105, 1)',
                        borderWidth: 2,
                        borderRadius: 10,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0b1329',
                        titleColor: '#ffffff',
                        bodyColor: '#cbd5e1',
                        padding: 14,
                        cornerRadius: 12,
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('id-ID')} orang`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                            color: '#64748b',
                            stepSize: 1,
                            callback: val => val.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });
    })();
    @endif
</script>
@endpush
