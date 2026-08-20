@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan rekap pendukung per TPS')

@section('content')
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
    @if(!$chartLabels->isEmpty())
    const ctx = document.getElementById('tpsChart').getContext('2d');

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
    @endif
</script>
@endpush
