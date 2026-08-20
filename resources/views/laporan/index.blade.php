@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')
@section('page-title', 'Laporan & Rekapitulasi Suara per TPS')
@section('page-subtitle', 'Matriks lengkap jumlah DPT, pendukung, non-pendukung, dan persentase capaian per TPS')

@section('header-actions')
    <a href="{{ route('laporan.print') }}" target="_blank"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-extrabold px-4 py-2.5 rounded-xl transition-all text-xs shadow-md shadow-emerald-900/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Cetak Laporan</span>
    </a>
@endsection

@section('content')
<div class="py-4 space-y-6">

    {{-- ===== STATISTIC CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total TPS</p>
            <p class="text-2xl font-extrabold text-slate-900 mt-1 tracking-tight">{{ number_format($totalTps) }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Pemilih (DPT)</p>
            <p class="text-2xl font-extrabold text-sky-600 mt-1 tracking-tight">{{ number_format($totalVoters) }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Pendukung</p>
            <p class="text-2xl font-extrabold text-emerald-600 mt-1 tracking-tight">{{ number_format($totalSupporters) }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Persentase Capaian</p>
            <p class="text-2xl font-extrabold text-emerald-600 mt-1 tracking-tight">{{ $overallPercentage }}%</p>
        </div>
    </div>

    {{-- ===== REKAP TABLE ===== --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base">Matriks Rekapitulasi per TPS</h3>
                <p class="text-slate-400 text-xs mt-0.5">Rincian lengkap data pendukung per Tempat Pemungutan Suara</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200 text-slate-500 text-[11px] font-extrabold uppercase tracking-wider">
                        <th class="px-6 py-4 w-12 text-center">#</th>
                        <th class="px-6 py-4">Nama TPS</th>
                        <th class="px-6 py-4 text-center">Total Pemilih</th>
                        <th class="px-6 py-4 text-center">Jumlah Pendukung</th>
                        <th class="px-6 py-4 text-center">Belum Pendukung</th>
                        <th class="px-6 py-4 text-center">% Pendukung</th>
                        <th class="px-6 py-4 text-center">Status Capaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rekapTps as $tps)
                    @php
                        $pct = $tps->voters_count > 0 ? round(($tps->supporters_count / $tps->voters_count) * 100, 1) : 0;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-center text-slate-400 font-semibold text-xs">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-extrabold text-slate-900 text-sm">{{ $tps->nama_tps }}</td>
                        <td class="px-6 py-4 text-center font-bold text-slate-700">{{ number_format($tps->voters_count) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                {{ number_format($tps->supporters_count) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-slate-500 font-medium">{{ number_format($tps->non_supporters_count) }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2.5">
                                <div class="w-20 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/60">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="font-extrabold text-xs text-slate-800">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($pct >= 50)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    🔥 Unggul (>50%)
                                </span>
                            @elseif($pct >= 30)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                    ⚡ Potensial (30-50%)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-600 border border-slate-200">
                                    📌 Perlu Ditingkatkan
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-semibold">Belum Ada Data TPS</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100/80 font-extrabold border-t border-slate-200 text-slate-900 text-sm">
                        <td colspan="2" class="px-6 py-4">TOTAL KESELURUHAN</td>
                        <td class="px-6 py-4 text-center text-sky-700">{{ number_format($totalVoters) }}</td>
                        <td class="px-6 py-4 text-center text-emerald-700">{{ number_format($totalSupporters) }}</td>
                        <td class="px-6 py-4 text-center text-slate-600">{{ number_format($totalNonSupporters) }}</td>
                        <td class="px-6 py-4 text-center text-emerald-700">{{ $overallPercentage }}%</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs bg-emerald-200 text-emerald-950 px-3 py-1 rounded-full font-extrabold">
                                Rekap Akhir
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
