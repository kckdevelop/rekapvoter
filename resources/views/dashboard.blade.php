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
                    <div class="inline-flex items-center gap-2 text-[10px] font-extrabold text-indigo-600 bg-indigo-50 border border-indigo-200 px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Live Quick Count</span>
                    </div>
                    <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Perolehan Suara Quick Count</h2>
                    <p class="text-slate-500 text-xs mt-0.5">
                        Distribusi suara sementara dari
                        <span id="live-tps-summary" class="font-bold text-indigo-600">{{ $totalQuickSubmitted }} dari {{ $totalTps }} TPS</span>
                        yang sudah menginput data
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0 flex-wrap sm:flex-nowrap">
                    <button type="button" onclick="openQuickCountFullscreen()" class="inline-flex items-center gap-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold px-3.5 py-2 rounded-xl transition-all shadow-sm active:scale-95 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        <span>Layar Penuh (Fullscreen)</span>
                    </button>
                    <a href="{{ route('rekap-quick.index') }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold px-3.5 py-2 rounded-xl transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Rekap Lengkap
                    </a>
                </div>
            </div>

            {{-- Empty State --}}
            <div id="quick-empty-state" class="{{ $hasQuickData ? 'hidden' : '' }} flex flex-col items-center justify-center py-16 text-center">
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

            {{-- PIE CHART CONTAINER --}}
            <div id="quick-chart-container" class="{{ $hasQuickData ? '' : 'hidden' }} flex flex-col sm:flex-row items-center gap-8">
                {{-- Chart Canvas --}}
                <div class="relative flex-shrink-0" style="width: 220px; height: 220px;">
                    <canvas id="quickPieChart"></canvas>
                    {{-- Centre label --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Suara Masuk</p>
                        <p id="live-quick-center-suara" class="text-2xl font-extrabold text-slate-900 leading-tight">{{ number_format($quickSuaraMasuk) }}</p>
                        <p class="text-[10px] font-bold text-slate-400 mt-0.5">suara</p>
                    </div>
                </div>

                {{-- Legend & Stats --}}
                <div class="flex-1 w-full space-y-3">
                    <div id="live-candidate-list" class="space-y-3">
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
                    </div>

                    {{-- Total Suara Sah --}}
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-500">Total Suara Sah</p>
                        <p class="text-sm font-extrabold text-slate-900"><span id="live-quick-suara-sah">{{ number_format($quickSuaraSah) }}</span> <span class="text-slate-400 font-medium text-xs">suara</span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Count Stats Panel --}}
        <div class="lg:col-span-2 flex flex-col gap-5">

            {{-- Card: TPS Sudah Input Quick Count --}}
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 text-white rounded-3xl p-6 shadow-lg shadow-indigo-950/20 flex-1 flex flex-col justify-between min-h-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0 text-xl backdrop-blur-md">⚡</div>
                    <div>
                        <p class="text-indigo-100 text-[11px] font-extrabold uppercase tracking-wider">TPS Input Quick Count</p>
                        <p class="text-3xl font-extrabold tracking-tight mt-0.5">
                            <span id="live-tps-submitted-count">{{ $totalQuickSubmitted }}</span><span class="text-indigo-300 text-lg font-bold"> / <span id="live-total-tps-count">{{ $totalTps }}</span></span>
                        </p>
                    </div>
                </div>
                {{-- Progress Bar --}}
                <div class="mt-5">
                    <div class="flex items-center justify-between mb-1.5">
                        <p class="text-indigo-200 text-xs font-bold">Progress Input</p>
                        <p id="live-tps-pct" class="text-white text-xs font-extrabold">{{ $pctTps }}%</p>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2 overflow-hidden">
                        <div id="live-tps-progress-bar" class="h-2 rounded-full bg-white transition-all duration-700" style="width: {{ $pctTps }}%"></div>
                    </div>
                    <p class="text-indigo-200 text-[10px] mt-1.5"><span id="live-tps-belum-input">{{ $tpsBelumInput }}</span> TPS belum input</p>
                </div>
            </div>

            {{-- Card: Surat Suara Masuk vs Total DPT --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex-1">
                @php
                    $suaraMasukColor = $pctSuaraMasuk >= 75
                        ? '#10b981'   // emerald
                        : ($pctSuaraMasuk >= 50 ? '#f59e0b' : '#6366f1'); // amber / indigo
                @endphp
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Surat Suara Masuk</p>
                <div class="flex items-end justify-between gap-2 mt-1">
                    <div>
                        <p id="live-suara-masuk-count" class="text-3xl font-extrabold tracking-tight text-slate-900 leading-none">
                            {{ number_format($quickSuaraMasuk) }}
                        </p>
                        <p class="text-xs text-slate-400 font-semibold mt-1">dari <span id="live-total-voters-count">{{ number_format($totalVoters) }}</span> DPT</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p id="live-partisipasi-pct" class="text-2xl font-black tracking-tight" style="color: {{ $suaraMasukColor }}">
                            {{ $pctSuaraMasuk }}%
                        </p>
                        <p class="text-[10px] text-slate-400 font-bold">partisipasi</p>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="mt-4 w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div id="live-partisipasi-bar" class="h-2.5 rounded-full transition-all duration-700"
                         style="width: {{ min(100, $pctSuaraMasuk) }}%; background-color: {{ $suaraMasukColor }}"></div>
                </div>

                {{-- Keterangan suara sah vs tidak sah --}}
                <div class="mt-3 flex items-center justify-between text-[11px] font-bold text-slate-500">
                    <span>Suara Sah: <span id="live-suara-sah-count" class="text-slate-800">{{ number_format($quickSuaraSah) }}</span></span>
                    <span>Tidak Sah: <span id="live-suara-tidak-sah-count" class="text-slate-800">{{ number_format($quickSuaraTidakSah) }}</span></span>
                </div>
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

    {{-- ===== FULLSCREEN LIVE QUICK COUNT BROADCAST MODAL ===== --}}
    <div id="quickCountFullscreenOverlay" class="hidden fixed inset-0 z-[99999] bg-slate-950 text-white flex flex-col justify-between overflow-y-auto selection:bg-indigo-500 selection:text-white" style="background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.2), transparent 60%), radial-gradient(circle at 100% 100%, rgba(16, 185, 129, 0.12), transparent 50%), #050814;">
        
        {{-- Header Bar --}}
        <div class="border-b border-slate-800/80 bg-slate-950/70 backdrop-blur-xl sticky top-0 z-50 px-4 sm:px-8 py-3.5 sm:py-4 shadow-xl">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-500 flex items-center justify-center shadow-lg shadow-indigo-950/50 flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-emerald-400 bg-emerald-950/70 border border-emerald-500/30 px-2.5 py-0.5 rounded-full shadow-inner">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                LIVE BROADCAST
                            </span>
                            <span class="text-slate-500 text-xs">•</span>
                            <span class="text-slate-400 text-xs font-semibold">Hitung Cepat Pilkades</span>
                        </div>
                        <h1 class="text-lg sm:text-xl font-black text-white tracking-tight leading-tight">Live Tabulasi Quick Count Pemilihan</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4 flex-wrap self-end md:self-auto">
                    {{-- Live Clock & Sync indicator --}}
                    <div class="flex items-center gap-3 bg-slate-900/90 border border-slate-800 px-4 py-2 rounded-2xl shadow-inner">
                        <div class="text-right">
                            <p id="fs-digital-clock" class="text-xs sm:text-sm font-black text-white font-mono tracking-wider">--:--:-- WIB</p>
                            <p class="text-[9px] font-bold text-emerald-400 uppercase tracking-widest flex items-center justify-end gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Auto-Sync Aktif
                            </p>
                        </div>
                    </div>

                    {{-- Close Fullscreen Button --}}
                    <button type="button" onclick="closeQuickCountFullscreen()" class="inline-flex items-center gap-2 bg-rose-600/90 hover:bg-rose-600 text-white font-extrabold text-xs px-4 py-2.5 rounded-2xl border border-rose-500/40 shadow-lg shadow-rose-950/40 transition-all active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tutup Layar Penuh <span class="hidden sm:inline font-mono opacity-80 text-[10px] bg-white/20 px-1.5 py-0.5 rounded ml-1">ESC</span></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Fullscreen Content Body --}}
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-8 py-6 flex-1 flex flex-col justify-between gap-6">

            {{-- Top 4 KPI Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- 1. Total Suara Masuk --}}
                <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-4 sm:p-5 backdrop-blur-md shadow-xl flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] font-extrabold text-indigo-400 uppercase tracking-wider">Total Suara Masuk</p>
                        <span class="text-lg">📥</span>
                    </div>
                    <div class="mt-1">
                        <p id="fs-suara-masuk-count" class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ number_format($quickSuaraMasuk) }}</p>
                        <p class="text-[11px] text-slate-400 font-semibold mt-1">Akumulasi seluruh TPS terinput</p>
                    </div>
                </div>

                {{-- 2. Tingkat Partisipasi --}}
                <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-4 sm:p-5 backdrop-blur-md shadow-xl flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] font-extrabold text-emerald-400 uppercase tracking-wider">Partisipasi DPT</p>
                        <span class="text-lg">📊</span>
                    </div>
                    <div>
                        <div class="flex items-baseline justify-between">
                            <p id="fs-partisipasi-pct" class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $pctSuaraMasuk }}%</p>
                            <p class="text-[11px] text-slate-400 font-medium">dari <span id="fs-total-voters-count" class="font-bold text-white">{{ number_format($totalVoters) }}</span> DPT</p>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-1.5 mt-2.5 overflow-hidden">
                            <div id="fs-partisipasi-bar" class="h-1.5 rounded-full bg-emerald-500 transition-all duration-700" style="width: {{ min(100, $pctSuaraMasuk) }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- 3. TPS Masuk --}}
                <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-4 sm:p-5 backdrop-blur-md shadow-xl flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] font-extrabold text-amber-400 uppercase tracking-wider">Progress TPS Masuk</p>
                        <span class="text-lg">⚡</span>
                    </div>
                    <div>
                        <div class="flex items-baseline justify-between">
                            <p class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                                <span id="fs-tps-submitted-count">{{ $totalQuickSubmitted }}</span><span class="text-slate-500 text-lg font-bold"> / <span id="fs-total-tps-count">{{ $totalTps }}</span></span>
                            </p>
                            <p id="fs-tps-pct" class="text-xs font-black text-amber-400">{{ $pctTps }}%</p>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-1.5 mt-2.5 overflow-hidden">
                            <div id="fs-tps-progress-bar" class="h-1.5 rounded-full bg-amber-400 transition-all duration-700" style="width: {{ $pctTps }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- 4. Suara Sah vs Tidak Sah --}}
                <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl p-4 sm:p-5 backdrop-blur-md shadow-xl flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] font-extrabold text-purple-400 uppercase tracking-wider">Status Validitas</p>
                        <span class="text-lg">⚖️</span>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-semibold">Suara Sah:</span>
                            <span id="fs-suara-sah-count" class="font-extrabold text-emerald-400">{{ number_format($quickSuaraSah) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-semibold">Tidak Sah:</span>
                            <span id="fs-suara-tidak-sah-count" class="font-extrabold text-rose-400">{{ number_format($quickSuaraTidakSah) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Data Section --}}
            <div id="fs-quick-empty-state" class="{{ $hasQuickData ? 'hidden' : '' }} flex-1 bg-slate-900/50 border border-slate-800/60 rounded-3xl p-12 flex flex-col items-center justify-center text-center">
                <div class="w-24 h-24 rounded-3xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center mb-6 animate-pulse">
                    <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-white">Menunggu Data Quick Count Pertama Masuk</h3>
                <p class="text-slate-400 text-sm max-w-md mt-2">Halaman fullscreen ini tetap live dan otomatis memuat perolehan suara secara real-time tanpa perlu refresh begitu saksi TPS mulai menginput.</p>
            </div>

            <div id="fs-quick-data-container" class="{{ $hasQuickData ? '' : 'hidden' }} flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                
                {{-- Left Column: Large Candidate Cards (7 cols) --}}
                <div class="lg:col-span-7 flex flex-col justify-center gap-4" id="fs-candidate-cards">
                    {{-- Populated dynamically via JS --}}
                </div>

                {{-- Right Column: Live Donut Chart & Detailed Breakdown (5 cols) --}}
                <div class="lg:col-span-5 bg-slate-900/70 border border-slate-800/80 rounded-3xl p-6 sm:p-8 backdrop-blur-md shadow-2xl flex flex-col items-center justify-between">
                    <div class="w-full text-center mb-2">
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Diagram Komposisi Perolehan</p>
                        <h3 class="text-base font-extrabold text-white">Distribusi Suara Quick Count</h3>
                    </div>

                    {{-- Donut Chart with central counter --}}
                    <div class="relative w-64 h-64 sm:w-72 sm:h-72 my-4 flex items-center justify-center flex-shrink-0">
                        <canvas id="fsQuickPieChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Masuk</span>
                            <span id="fs-live-quick-center-suara" class="text-2xl sm:text-3xl font-black text-white leading-tight">{{ number_format($quickSuaraMasuk) }}</span>
                            <span class="text-[10px] font-bold text-indigo-400 mt-0.5">Suara Terkumpul</span>
                        </div>
                    </div>

                    {{-- Mini legend --}}
                    <div id="fs-legend-breakdown" class="w-full grid grid-cols-1 sm:grid-cols-2 gap-2 pt-4 border-t border-slate-800/80">
                        {{-- Populated by JS --}}
                    </div>
                </div>

            </div>

        </div>

        {{-- Footer Bar --}}
        <div class="border-t border-slate-800/80 bg-slate-950/70 backdrop-blur-xl px-4 sm:px-8 py-3 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>© {{ date('Y') }} Sistem Rekap & Quick Count Suara Pemilihan</p>
            <p class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Sinkronisasi Realtime Terakhir: <span id="fs-last-update-time" class="text-slate-300 font-mono font-bold">Baru saja</span>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ===== LIVE QUICK COUNT REAL-TIME & FULLSCREEN LOGIC =====
    let quickChart = null;
    let fsQuickChart = null;

    // Cache current live data object
    let currentLiveQuickData = {
        totalTps: {{ $totalTps }},
        totalVoters: {{ $totalVoters }},
        totalSupporters: {{ $totalSupporters }},
        totalQuickSubmitted: {{ $totalQuickSubmitted }},
        pctTps: {{ $pctTps }},
        tpsBelumInput: {{ $tpsBelumInput }},
        quickSuaraMasuk: {{ $quickSuaraMasuk }},
        quickSuaraSah: {{ $quickSuaraSah }},
        quickSuaraTidakSah: {{ $quickSuaraTidakSah }},
        pctSuaraMasuk: {{ $pctSuaraMasuk }},
        hasQuickData: {{ $hasQuickData ? 'true' : 'false' }},
        quickPieLabels: @json($quickPieLabels),
        quickPieData: @json($quickPieData),
        quickPieColors: @json($quickPieColors),
        candidateBreakdown: @json($candidateBreakdown),
    };

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // --- Digital Clock Routine for Fullscreen View ---
    function updateDigitalClock() {
        const clockEl = document.getElementById('fs-digital-clock');
        if (clockEl) {
            const now = new Date();
            const hrs = String(now.getHours()).padStart(2, '0');
            const mins = String(now.getMinutes()).padStart(2, '0');
            const secs = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${hrs}:${mins}:${secs} WIB`;
        }
    }
    setInterval(updateDigitalClock, 1000);
    updateDigitalClock();

    // --- Main Dashboard Doughnut Chart ---
    function initOrUpdateQuickChart(labels, data, colors) {
        const pieCanvas = document.getElementById('quickPieChart');
        if (!pieCanvas) return;

        const hoverColors = colors.map(c => c + 'cc');

        if (!quickChart) {
            quickChart = new Chart(pieCanvas.getContext('2d'), {
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
                        duration: 600,
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
        } else {
            quickChart.data.labels = labels;
            quickChart.data.datasets[0].data = data;
            quickChart.data.datasets[0].backgroundColor = colors;
            quickChart.data.datasets[0].hoverBackgroundColor = hoverColors;
            quickChart.update();
        }
    }

    // --- Fullscreen Doughnut Chart ---
    function initOrUpdateFsQuickChart(labels, data, colors) {
        const fsCanvas = document.getElementById('fsQuickPieChart');
        if (!fsCanvas) return;

        const hoverColors = colors.map(c => c + 'cc');

        if (!fsQuickChart) {
            fsQuickChart = new Chart(fsCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        hoverBackgroundColor: hoverColors,
                        borderColor: '#0b1329',
                        borderWidth: 4,
                        hoverBorderWidth: 5,
                        hoverOffset: 10,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    animation: {
                        animateRotate: true,
                        animateScale: false,
                        duration: 600,
                        easing: 'easeOutQuart',
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#ffffff',
                            bodyColor: '#cbd5e1',
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
        } else {
            fsQuickChart.data.labels = labels;
            fsQuickChart.data.datasets[0].data = data;
            fsQuickChart.data.datasets[0].backgroundColor = colors;
            fsQuickChart.data.datasets[0].hoverBackgroundColor = hoverColors;
            fsQuickChart.update();
        }
    }

    // --- Render Candidate Lists (Standard Dashboard) ---
    function renderCandidateList(candidates) {
        const container = document.getElementById('live-candidate-list');
        if (!container || !candidates) return;

        let html = '';
        candidates.forEach(c => {
            html += `
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: ${c.warna}"></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1 gap-2">
                        <p class="text-xs font-extrabold text-slate-800 truncate">No. ${c.nomor_urut} — ${escapeHtml(c.nama)}</p>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-xs font-black text-slate-900">${c.suara.toLocaleString('id-ID')}</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md text-white" style="background-color: ${c.warna}">${c.pct}%</span>
                        </div>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full transition-all duration-700" style="width: ${Math.min(100, c.pct)}%; background-color: ${c.warna}"></div>
                    </div>
                </div>
            </div>`;
        });
        container.innerHTML = html;
    }

    // --- Render Fullscreen Big Candidate Cards & Legend ---
    function renderFsCandidateCards(candidates, quickSuaraMasuk) {
        const cardsContainer = document.getElementById('fs-candidate-cards');
        const legendContainer = document.getElementById('fs-legend-breakdown');
        if (!cardsContainer || !candidates) return;

        const maxVotes = Math.max(...candidates.map(c => c.suara), 0);
        let cardsHtml = '';
        let legendHtml = '';

        candidates.forEach(c => {
            const isLeading = maxVotes > 0 && c.suara === maxVotes;
            const nomorFormatted = String(c.nomor_urut).padStart(2, '0');

            cardsHtml += `
            <div class="relative bg-slate-900/85 border ${isLeading ? 'border-amber-500/60 shadow-amber-500/10' : 'border-slate-800'} rounded-3xl p-5 sm:p-6 shadow-xl backdrop-blur-md overflow-hidden transition-all duration-300">
                ${isLeading ? `
                <div class="absolute top-0 right-0 bg-gradient-to-l from-amber-500 to-amber-600 text-slate-950 font-black text-[10px] sm:text-xs px-3.5 py-1 rounded-bl-2xl uppercase tracking-wider shadow-md flex items-center gap-1.5">
                    <span>👑 Unggul Sementara</span>
                </div>` : ''}

                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-xl text-white shadow-lg flex-shrink-0" style="background-color: ${c.warna}">
                        ${nomorFormatted}
                    </div>
                    <div class="flex-1 min-w-0 pr-12">
                        <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Pasangan Calon No. ${c.nomor_urut}</p>
                        <h4 class="text-base sm:text-lg font-black text-white truncate tracking-tight">${escapeHtml(c.nama)}</h4>
                    </div>
                </div>
                
                <div class="flex items-end justify-between gap-4 mb-2.5">
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Perolehan Suara</p>
                        <p class="text-2xl sm:text-3xl font-black text-white tracking-tight">${c.suara.toLocaleString('id-ID')} <span class="text-xs sm:text-sm font-semibold text-slate-400">suara</span></p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block text-base sm:text-xl font-black px-3.5 py-1 rounded-xl text-white shadow-md" style="background-color: ${c.warna}">
                            ${c.pct}%
                        </span>
                    </div>
                </div>

                <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden p-0.5 border border-slate-700/50">
                    <div class="h-2 rounded-full transition-all duration-700 shadow-sm" style="width: ${Math.min(100, c.pct)}%; background-color: ${c.warna}"></div>
                </div>
            </div>`;

            legendHtml += `
            <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-800/60 border border-slate-800 text-xs">
                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: ${c.warna}"></span>
                <span class="text-slate-300 font-bold truncate flex-1">No. ${c.nomor_urut} - ${escapeHtml(c.nama)}</span>
                <span class="text-white font-black">${c.pct}%</span>
            </div>`;
        });

        cardsContainer.innerHTML = cardsHtml;
        if (legendContainer) legendContainer.innerHTML = legendHtml;
    }

    // --- Update Fullscreen UI Elements ---
    function updateFullscreenUI(data) {
        if (!data) return;

        // 1. Update KPI Summary Cards
        const suaraMasukCount = document.getElementById('fs-suara-masuk-count');
        if (suaraMasukCount) suaraMasukCount.textContent = data.quickSuaraMasuk.toLocaleString('id-ID');

        const partisipasiPct = document.getElementById('fs-partisipasi-pct');
        if (partisipasiPct) partisipasiPct.textContent = `${data.pctSuaraMasuk}%`;

        const totalVotersCount = document.getElementById('fs-total-voters-count');
        if (totalVotersCount) totalVotersCount.textContent = data.totalVoters.toLocaleString('id-ID');

        const partisipasiBar = document.getElementById('fs-partisipasi-bar');
        if (partisipasiBar) partisipasiBar.style.width = `${Math.min(100, data.pctSuaraMasuk)}%`;

        const tpsSubmitted = document.getElementById('fs-tps-submitted-count');
        if (tpsSubmitted) tpsSubmitted.textContent = data.totalQuickSubmitted;

        const totalTps = document.getElementById('fs-total-tps-count');
        if (totalTps) totalTps.textContent = data.totalTps;

        const tpsPct = document.getElementById('fs-tps-pct');
        if (tpsPct) tpsPct.textContent = `${data.pctTps}%`;

        const tpsProgBar = document.getElementById('fs-tps-progress-bar');
        if (tpsProgBar) tpsProgBar.style.width = `${data.pctTps}%`;

        const suaraSah = document.getElementById('fs-suara-sah-count');
        if (suaraSah) suaraSah.textContent = data.quickSuaraSah.toLocaleString('id-ID');

        const suaraTidakSah = document.getElementById('fs-suara-tidak-sah-count');
        if (suaraTidakSah) suaraTidakSah.textContent = data.quickSuaraTidakSah.toLocaleString('id-ID');

        // 2. Toggle Empty State vs Data Container
        const emptyState = document.getElementById('fs-quick-empty-state');
        const dataContainer = document.getElementById('fs-quick-data-container');

        if (data.hasQuickData) {
            if (emptyState) emptyState.classList.add('hidden');
            if (dataContainer) dataContainer.classList.remove('hidden');

            renderFsCandidateCards(data.candidateBreakdown, data.quickSuaraMasuk);
            initOrUpdateFsQuickChart(data.quickPieLabels, data.quickPieData, data.quickPieColors);

            const centerSuara = document.getElementById('fs-live-quick-center-suara');
            if (centerSuara) centerSuara.textContent = data.quickSuaraMasuk.toLocaleString('id-ID');
        } else {
            if (emptyState) emptyState.classList.remove('hidden');
            if (dataContainer) dataContainer.classList.add('hidden');
        }

        // Update Last Sync Timestamp
        const lastUpdateEl = document.getElementById('fs-last-update-time');
        if (lastUpdateEl) {
            const now = new Date();
            lastUpdateEl.textContent = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')} WIB`;
        }
    }

    // --- Fullscreen Toggle Logic ---
    function openQuickCountFullscreen() {
        const overlay = document.getElementById('quickCountFullscreenOverlay');
        if (!overlay) return;

        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Trigger native browser fullscreen
        if (overlay.requestFullscreen) {
            overlay.requestFullscreen().catch(() => {});
        } else if (overlay.webkitRequestFullscreen) {
            overlay.webkitRequestFullscreen().catch(() => {});
        } else if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen().catch(() => {});
        }

        // Update Fullscreen UI immediately with cached data
        updateFullscreenUI(currentLiveQuickData);
    }

    function closeQuickCountFullscreen() {
        const overlay = document.getElementById('quickCountFullscreenOverlay');
        if (overlay) {
            overlay.classList.add('hidden');
        }
        document.body.style.overflow = '';

        if (document.fullscreenElement || document.webkitFullscreenElement) {
            if (document.exitFullscreen) {
                document.exitFullscreen().catch(() => {});
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen().catch(() => {});
            }
        }
    }

    // Listen for browser ESC / exit fullscreen events
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
            const overlay = document.getElementById('quickCountFullscreenOverlay');
            if (overlay && !overlay.classList.contains('hidden')) {
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
    });
    document.addEventListener('webkitfullscreenchange', () => {
        if (!document.webkitFullscreenElement) {
            const overlay = document.getElementById('quickCountFullscreenOverlay');
            if (overlay && !overlay.classList.contains('hidden')) {
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeQuickCountFullscreen();
        }
    });

    // Initial Initializations
    @if($hasQuickData)
    initOrUpdateQuickChart(@json($quickPieLabels), @json($quickPieData), @json($quickPieColors));
    updateFullscreenUI(currentLiveQuickData);
    @endif

    // ===== Real-time Polling Routine =====
    async function updateDashboardLive() {
        if (document.hidden) return; // Pause polling when tab is inactive

        try {
            const response = await fetch('{{ route('dashboard.live-data') }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) return;
            const data = await response.json();
            currentLiveQuickData = data;

            // 1. Update Header Summary
            const tpsSummary = document.getElementById('live-tps-summary');
            if (tpsSummary) tpsSummary.textContent = `${data.totalQuickSubmitted} dari ${data.totalTps} TPS`;

            // 2. Toggle Standard Empty State vs Chart Container
            const emptyState = document.getElementById('quick-empty-state');
            const chartContainer = document.getElementById('quick-chart-container');

            if (data.hasQuickData) {
                if (emptyState) emptyState.classList.add('hidden');
                if (chartContainer) chartContainer.classList.remove('hidden');

                // Update Doughnut Chart & Center Text
                initOrUpdateQuickChart(data.quickPieLabels, data.quickPieData, data.quickPieColors);
                const centerSuara = document.getElementById('live-quick-center-suara');
                if (centerSuara) centerSuara.textContent = data.quickSuaraMasuk.toLocaleString('id-ID');

                // Update Candidates List
                renderCandidateList(data.candidateBreakdown);

                // Update Total Suara Sah
                const suaraSahQuick = document.getElementById('live-quick-suara-sah');
                if (suaraSahQuick) suaraSahQuick.textContent = data.quickSuaraSah.toLocaleString('id-ID');
            } else {
                if (emptyState) emptyState.classList.remove('hidden');
                if (chartContainer) chartContainer.classList.add('hidden');
            }

            // 3. Update TPS Input Quick Count Card
            const tpsSubCount = document.getElementById('live-tps-submitted-count');
            if (tpsSubCount) tpsSubCount.textContent = data.totalQuickSubmitted;

            const tpsTotalCount = document.getElementById('live-total-tps-count');
            if (tpsTotalCount) tpsTotalCount.textContent = data.totalTps;

            const tpsPct = document.getElementById('live-tps-pct');
            if (tpsPct) tpsPct.textContent = `${data.pctTps}%`;

            const tpsProgBar = document.getElementById('live-tps-progress-bar');
            if (tpsProgBar) tpsProgBar.style.width = `${data.pctTps}%`;

            const tpsBelumInput = document.getElementById('live-tps-belum-input');
            if (tpsBelumInput) tpsBelumInput.textContent = data.tpsBelumInput;

            // 4. Update Surat Suara Masuk Card
            const suaraMasukCount = document.getElementById('live-suara-masuk-count');
            if (suaraMasukCount) suaraMasukCount.textContent = data.quickSuaraMasuk.toLocaleString('id-ID');

            const totalVotersCount = document.getElementById('live-total-voters-count');
            if (totalVotersCount) totalVotersCount.textContent = data.totalVoters.toLocaleString('id-ID');

            const partisipasiColor = data.pctSuaraMasuk >= 75
                ? '#10b981'
                : (data.pctSuaraMasuk >= 50 ? '#f59e0b' : '#6366f1');

            const partisipasiPct = document.getElementById('live-partisipasi-pct');
            if (partisipasiPct) {
                partisipasiPct.textContent = `${data.pctSuaraMasuk}%`;
                partisipasiPct.style.color = partisipasiColor;
            }

            const partisipasiBar = document.getElementById('live-partisipasi-bar');
            if (partisipasiBar) {
                partisipasiBar.style.width = `${Math.min(100, data.pctSuaraMasuk)}%`;
                partisipasiBar.style.backgroundColor = partisipasiColor;
            }

            const statSuaraSah = document.getElementById('live-suara-sah-count');
            if (statSuaraSah) statSuaraSah.textContent = data.quickSuaraSah.toLocaleString('id-ID');

            const statSuaraTidakSah = document.getElementById('live-suara-tidak-sah-count');
            if (statSuaraTidakSah) statSuaraTidakSah.textContent = data.quickSuaraTidakSah.toLocaleString('id-ID');

            // 5. Update Fullscreen View
            updateFullscreenUI(data);

        } catch (e) {
            console.error('Error fetching dashboard live data:', e);
        }
    }

    // Run live auto-refresh every 3 seconds
    setInterval(updateDashboardLive, 3000);

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
