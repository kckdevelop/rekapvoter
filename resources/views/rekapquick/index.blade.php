@extends('layouts.app')

@section('title', 'Rekap Quick vs Pendukung')
@section('page-title', 'Rekapitulasi Quick Count vs Data Pendukung per TPS')
@section('page-subtitle', 'Matriks komparasi capaian suara cepat (Quick Count) hasil pemilihan per calon dibandingkan dengan target pendukung terdaftar')

@section('header-actions')
    <a href="{{ route('rekap-quick.print') }}" target="_blank"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-extrabold px-4 py-2.5 rounded-xl transition-all text-xs shadow-md shadow-indigo-900/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Cetak Laporan Quick Count</span>
    </a>
@endsection

@section('content')
@php
    $mainCandidate = $candidates->firstWhere('is_main_candidate', true) ?? $candidates->first();
@endphp
<div class="py-4 space-y-6">

    {{-- ===== STATISTIC CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Card: Total Pendukung vs Quick --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-extrabold text-lg shadow-inner">
                ⭐
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Target Pendukung</p>
                <p class="text-2xl font-extrabold text-amber-600 mt-0.5 tracking-tight">{{ number_format($totalSupporters) }}</p>
            </div>
        </div>

        {{-- Card: Hasil Suara Quick Count --}}
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 text-white rounded-3xl p-5 shadow-lg shadow-indigo-950/20 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center font-extrabold text-lg backdrop-blur-md">
                ⚡
            </div>
            <div>
                <p class="text-indigo-100 text-[11px] font-extrabold uppercase tracking-wider">Quick Count ({{ Str::words($mainCandidate->nama ?? 'Kandidat', 2) }})</p>
                <p class="text-2xl font-extrabold tracking-tight mt-0.5">{{ number_format($totalSuaraKandidat) }}</p>
            </div>
        </div>

        {{-- Card: Selisih (Quick vs Pendukung) --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl {{ $selisihKandidatVsPendukung >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center font-extrabold text-lg shadow-inner">
                {{ $selisihKandidatVsPendukung >= 0 ? '📈' : '📉' }}
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Selisih Quick vs Target</p>
                <p class="text-2xl font-extrabold {{ $selisihKandidatVsPendukung >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-0.5 tracking-tight">
                    {{ $selisihKandidatVsPendukung >= 0 ? '+' : '' }}{{ number_format($selisihKandidatVsPendukung) }}
                </p>
            </div>
        </div>

        {{-- Card: Persentase & Status Keberhasilan Target Pendukung --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center font-extrabold text-lg shadow-inner">
                🎯
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Capaian Target Pendukung</p>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-2xl font-extrabold text-sky-600 tracking-tight">{{ $persentaseKonversi }}%</span>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-{{ $badgeColorOverall }}-100 text-{{ $badgeColorOverall }}-800 border border-{{ $badgeColorOverall }}-200">
                        {{ $statusKeberhasilanOverall }}
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== RINGKASAN PERSENTASE CAPAIAN TARGET QUICK COUNT ===== --}}
    <div class="bg-slate-900 text-white rounded-3xl p-6 shadow-xl border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-1 text-center md:text-left">
            <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest bg-indigo-500/10 px-3 py-1 rounded-full border border-indigo-500/20">
                ⚡ Perolehan Quick Count vs Target Pendukung
            </span>
            <h3 class="text-xl font-extrabold text-white tracking-tight mt-2">
                {{ $mainCandidate->nama ?? 'Nurma Setiawan' }}: <span class="text-indigo-400 font-black">{{ number_format($totalSuaraKandidat) }} Suara Quick</span>
                <span class="text-slate-400 text-base font-normal">vs Target:</span>
                <span class="text-amber-400 font-bold">{{ number_format($totalSupporters) }} Pendukung</span>
            </h3>
            <p class="text-xs text-slate-400">
                Total TPS Terinput: <span class="text-white font-bold">{{ $tpsSubmittedCount }} dari {{ $totalTps }} TPS</span>
                • Status Capaian Target: <span class="text-indigo-400 font-extrabold">{{ $statusKeberhasilanOverall }}</span>
            </p>
        </div>

        <div class="flex items-center gap-6 flex-shrink-0">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-bold text-slate-400 uppercase">Capaian Target</p>
                <p class="text-3xl font-extrabold text-indigo-400 mt-0.5">{{ $persentaseKonversi }}%</p>
            </div>
            <div class="w-20 h-20 rounded-full border-4 border-indigo-500/30 flex items-center justify-center bg-slate-800 shadow-inner">
                <span class="text-lg font-black text-indigo-400">{{ $persentaseKonversi }}%</span>
            </div>
        </div>
    </div>

    {{-- ===== REKAP TABLE ===== --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base">Matriks Komparasi Quick Count vs Pendukung per TPS</h3>
                <p class="text-slate-400 text-xs mt-0.5">Rincian perolehan suara quick count calon dibandingkan dengan target pendukung terdaftar &amp; status persen keberhasilan</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse min-w-[950px]">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200 text-slate-500 text-[11px] font-extrabold uppercase tracking-wider">
                        <th class="px-5 py-4 w-12 text-center">#</th>
                        <th class="px-5 py-4">Nama TPS</th>
                        <th class="px-4 py-4 text-center">Total DPT</th>
                        <th class="px-4 py-4 text-center">Target Pendukung</th>
                        <th class="px-4 py-4 text-center">
                            <span class="inline-block px-2.5 py-1 rounded-lg text-white font-black text-[10px] bg-indigo-600">
                                No. {{ sprintf('%02d', $mainCandidate->nomor_urut ?? 1) }}
                            </span>
                            <span class="block text-xs font-extrabold text-slate-800 mt-1">Suara Quick ({{ Str::words($mainCandidate->nama ?? 'Kandidat', 2) }})</span>
                        </th>
                        <th class="px-4 py-4 text-center">Selisih (Quick - Target)</th>
                        <th class="px-4 py-4 text-center">% Konversi Target</th>
                        <th class="px-4 py-4 text-center">Status Keberhasilan Target</th>
                        <th class="px-4 py-4 text-center">Tidak Sah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rekapTps as $tps)
                    @php
                        $suaraQuick = $tps->quick_suara_kandidat ?? 0;
                        $targetPendukung = $tps->supporters_count ?? 0;
                        $selisih = $suaraQuick - $targetPendukung;

                        if ($targetPendukung > 0) {
                            $pctKonversi = round(($suaraQuick / $targetPendukung) * 100, 1);
                        } else {
                            $pctKonversi = $suaraQuick > 0 ? 100 : 0;
                        }
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-4 text-center text-slate-400 font-semibold text-xs">{{ $loop->iteration }}</td>
                        <td class="px-5 py-4 font-extrabold text-slate-900 text-sm">
                            {{ $tps->nama_tps }}
                        </td>
                        <td class="px-4 py-4 text-center font-bold text-slate-700">{{ number_format($tps->voters_count) }}</td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                ⭐ {{ number_format($targetPendukung) }}
                            </span>
                        </td>

                        {{-- Main Candidate Quick Votes --}}
                        <td class="px-4 py-4 text-center">
                            @if($tps->quick_is_submitted)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-extrabold bg-indigo-100 text-indigo-900 border border-indigo-300">
                                    ⚡ {{ number_format($suaraQuick) }}
                                </span>
                            @else
                                <span class="text-slate-300 font-bold">—</span>
                            @endif
                        </td>

                        {{-- Selisih Quick - Target --}}
                        <td class="px-4 py-4 text-center">
                            @if($tps->quick_is_submitted)
                                @if($selisih > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        +{{ number_format($selisih) }}
                                    </span>
                                @elseif($selisih == 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                        Pas (0)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                        {{ number_format($selisih) }}
                                    </span>
                                @endif
                            @else
                                <span class="text-slate-300 font-bold">—</span>
                            @endif
                        </td>

                        {{-- % Konversi --}}
                        <td class="px-4 py-4 text-center">
                            @if($tps->quick_is_submitted)
                                @if($targetPendukung > 0)
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-14 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/60">
                                            <div class="{{ $pctKonversi >= 100 ? 'bg-indigo-500' : ($pctKonversi >= 75 ? 'bg-amber-500' : 'bg-rose-500') }} h-2 rounded-full" style="width: {{ min(100, $pctKonversi) }}%"></div>
                                        </div>
                                        <span class="font-extrabold text-xs text-slate-800">{{ $pctKonversi }}%</span>
                                    </div>
                                @else
                                    <span class="text-xs font-extrabold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                                        {{ $suaraQuick > 0 ? '100%+' : '0%' }}
                                    </span>
                                @endif
                            @else
                                <span class="text-slate-300 font-bold">—</span>
                            @endif
                        </td>

                        {{-- Status Keberhasilan Target Pendukung --}}
                        <td class="px-4 py-4 text-center">
                            @if(!$tps->quick_is_submitted)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                    ⏳ Belum Input
                                </span>
                            @elseif($targetPendukung == 0)
                                @if($suaraQuick > 0)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-800 border border-indigo-300 shadow-sm" title="Hasil quick count suara kandidat utama melampaui target awal (belum ada target)">
                                        🚀 Surplus +{{ number_format($suaraQuick) }} Suara
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                        ⚪ Belum Ada Target
                                    </span>
                                @endif
                            @elseif($pctKonversi > 100)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm" title="Hasil suara quick count melampaui target pendukung terdaftar">
                                    🔥 Melampaui ({{ $pctKonversi }}%)
                                </span>
                            @elseif($pctKonversi == 100)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-teal-100 text-teal-800 border border-teal-300 shadow-sm" title="Target pendukung 100% terkonversi menjadi suara quick count">
                                    ✅ Target 100% Tercapai
                                </span>
                            @elseif($pctKonversi >= 75)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300 shadow-sm" title="Capaian target pendukung tergolong tinggi">
                                    ⚡ High ({{ $pctKonversi }}%)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-300 shadow-sm" title="Perolehan suara quick di bawah 75% dari target pendukung">
                                    📌 Belum Tercapai ({{ $pctKonversi }}%)
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-4 text-center text-amber-700 font-medium">
                            {{ $tps->quick_is_submitted ? number_format($tps->quick_suara_tidak_sah) : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-slate-400 font-semibold">Belum Ada Data TPS</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-900 font-extrabold border-t border-slate-800 text-white text-sm">
                        <td colspan="2" class="px-5 py-4">TOTAL KESELURUHAN</td>
                        <td class="px-4 py-4 text-center text-slate-300">{{ number_format($totalDpt) }}</td>
                        <td class="px-4 py-4 text-center text-amber-400">{{ number_format($totalSupporters) }}</td>
                        <td class="px-4 py-4 text-center text-indigo-400">{{ number_format($totalSuaraKandidat) }}</td>
                        <td class="px-4 py-4 text-center {{ $selisihKandidatVsPendukung >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $selisihKandidatVsPendukung >= 0 ? '+' : '' }}{{ number_format($selisihKandidatVsPendukung) }}
                        </td>
                        <td class="px-4 py-4 text-center text-sky-400">{{ $persentaseKonversi }}%</td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-xs bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-3 py-1 rounded-full font-bold">
                                {{ $statusKeberhasilanOverall }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center text-amber-300">{{ number_format($totalSuaraTidakSah) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
