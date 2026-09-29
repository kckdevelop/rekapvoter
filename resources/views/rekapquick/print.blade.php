<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekapitulasi Quick Count vs Pendukung — Kalurahan Sabdodadi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; color: black !important; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-6">

    {{-- Print Control Floating Bar --}}
    <div class="no-print max-w-5xl mx-auto mb-6 bg-slate-900 text-white p-4 rounded-2xl flex items-center justify-between shadow-xl">
        <div>
            <h2 class="font-extrabold text-sm">Laporan Hasil Quick Count vs Data Pendukung</h2>
            <p class="text-xs text-slate-400">Siap untuk dicetak atau disimpan sebagai PDF</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold px-5 py-2.5 rounded-xl text-xs shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan</span>
            </button>
            <button onclick="window.close()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold px-4 py-2.5 rounded-xl text-xs">
                Tutup
            </button>
        </div>
    </div>

    {{-- Printable Paper Sheet --}}
    <div class="max-w-5xl mx-auto bg-white p-8 sm:p-10 rounded-3xl shadow-sm border border-slate-200">
        
        {{-- Header Kertas --}}
        <div class="border-b-2 border-slate-900 pb-6 mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 uppercase">Laporan Quick Count &amp; Rekapitulasi Cepat</h1>
                <p class="text-sm font-extrabold text-indigo-700 mt-1">Calon Lurah: Nurma Setiawan, SE</p>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Kalurahan Sabdodadi — Tahun Pemilihan 2026</p>
            </div>
            <div class="text-right text-xs text-slate-500 font-semibold">
                <p>Dicetak Pada:</p>
                <p class="font-extrabold text-slate-800 text-sm mt-0.5">{{ now()->format('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        {{-- Summary Cards --}}
        @php
            $mainCandidate = $candidates->firstWhere('is_main_candidate', true) ?? $candidates->first();
        @endphp
        <div class="grid grid-cols-4 gap-3 mb-6">
            <div class="border border-slate-200 rounded-xl p-3 text-center bg-slate-50">
                <p class="text-[10px] font-bold text-slate-500 uppercase">Total DPT</p>
                <p class="text-lg font-black text-slate-900 mt-0.5">{{ number_format($totalDpt) }}</p>
            </div>
            <div class="border border-amber-200 rounded-xl p-3 text-center bg-amber-50">
                <p class="text-[10px] font-bold text-amber-800 uppercase">Target Pendukung</p>
                <p class="text-lg font-black text-amber-900 mt-0.5">{{ number_format($totalSupporters) }}</p>
            </div>
            <div class="border border-indigo-200 rounded-xl p-3 text-center bg-indigo-50">
                <p class="text-[10px] font-bold text-indigo-800 uppercase">Quick {{ Str::words($mainCandidate->nama ?? 'Kandidat', 2) }}</p>
                <p class="text-lg font-black text-indigo-900 mt-0.5">{{ number_format($totalSuaraKandidat) }}</p>
            </div>
            <div class="border border-sky-200 rounded-xl p-3 text-center bg-sky-50">
                <p class="text-[10px] font-bold text-sky-800 uppercase">Capaian Target</p>
                <p class="text-lg font-black text-sky-700 mt-0.5">{{ $persentaseKonversi }}%</p>
            </div>
        </div>

        {{-- Tabel Matriks Rekap Quick --}}
        <table class="w-full text-xs text-left border-collapse border border-slate-300 mb-8">
            <thead>
                <tr class="bg-slate-100 text-slate-900 border-b border-slate-300 font-extrabold uppercase">
                    <th class="border border-slate-300 p-2 text-center w-8">#</th>
                    <th class="border border-slate-300 p-2">Nama TPS</th>
                    <th class="border border-slate-300 p-2 text-center">DPT</th>
                    <th class="border border-slate-300 p-2 text-center">Target Pendukung</th>
                    <th class="border border-slate-300 p-2 text-center bg-indigo-50 text-indigo-950">
                        No. {{ sprintf('%02d', $mainCandidate->nomor_urut ?? 1) }} {{ Str::words($mainCandidate->nama ?? 'Kandidat', 2) }}
                    </th>
                    <th class="border border-slate-300 p-2 text-center">Selisih</th>
                    <th class="border border-slate-300 p-2 text-center">% Konversi Target</th>
                    <th class="border border-slate-300 p-2 text-center">Status Keberhasilan Target</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapTps as $tps)
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
                <tr class="border-b border-slate-200">
                    <td class="border border-slate-300 p-2 text-center text-slate-500 font-semibold">{{ $loop->iteration }}</td>
                    <td class="border border-slate-300 p-2 font-bold text-slate-900">{{ $tps->nama_tps }}</td>
                    <td class="border border-slate-300 p-2 text-center font-semibold">{{ number_format($tps->voters_count) }}</td>
                    <td class="border border-slate-300 p-2 text-center font-bold text-amber-900">{{ number_format($targetPendukung) }}</td>

                    <td class="border border-slate-300 p-2 text-center font-bold bg-indigo-50 text-indigo-900">
                        {{ $tps->quick_is_submitted ? number_format($suaraQuick) : '—' }}
                    </td>

                    <td class="border border-slate-300 p-2 text-center font-bold">
                        @if($tps->quick_is_submitted)
                            {{ $selisih >= 0 ? '+'.$selisih : $selisih }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="border border-slate-300 p-2 text-center font-extrabold">
                        @if($tps->quick_is_submitted)
                            {{ $targetPendukung > 0 ? $pctKonversi.'%' : ($suaraQuick > 0 ? '100%+' : '0%') }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="border border-slate-300 p-2 text-center font-bold">
                        @if(!$tps->quick_is_submitted)
                            <span class="text-slate-400 font-normal">Belum Input</span>
                        @elseif($targetPendukung == 0)
                            <span class="text-indigo-700">{{ $suaraQuick > 0 ? 'Surplus +'.$suaraQuick : 'Tanpa Target' }}</span>
                        @elseif($pctKonversi > 100)
                            <span class="text-emerald-700">Melampaui ({{ $pctKonversi }}%)</span>
                        @elseif($pctKonversi == 100)
                            <span class="text-teal-700">100% Tercapai</span>
                        @elseif($pctKonversi >= 75)
                            <span class="text-amber-700">High ({{ $pctKonversi }}%)</span>
                        @else
                            <span class="text-rose-700">Belum Tercapai ({{ $pctKonversi }}%)</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-slate-100 font-extrabold text-slate-900">
                    <td colspan="2" class="border border-slate-300 p-2.5">TOTAL KESELURUHAN</td>
                    <td class="border border-slate-300 p-2.5 text-center">{{ number_format($totalDpt) }}</td>
                    <td class="border border-slate-300 p-2.5 text-center text-amber-900">{{ number_format($totalSupporters) }}</td>
                    <td class="border border-slate-300 p-2.5 text-center bg-indigo-100 text-indigo-900">{{ number_format($totalSuaraKandidat) }}</td>
                    <td class="border border-slate-300 p-2.5 text-center">{{ number_format($totalSuaraKandidat - $totalSupporters) }}</td>
                    <td class="border border-slate-300 p-2.5 text-center">{{ $persentaseKonversi }}%</td>
                    <td class="border border-slate-300 p-2.5 text-center font-extrabold text-indigo-800">
                        {{ $totalSupporters == 0 ? ($totalSuaraKandidat > 0 ? 'Surplus' : 'Belum Ada Target') : ($persentaseKonversi >= 100 ? 'Melampaui Target' : ($persentaseKonversi >= 75 ? 'Capaian High' : 'Belum Tercapai')) }}
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Signature Tanda Tangan Saksi & Tim Sukses --}}
        <div class="mt-12 grid grid-cols-2 text-center text-xs">
            <div>
                <p class="font-bold text-slate-600">Mengetahui,</p>
                <p class="font-bold text-slate-800 mt-0.5">Ketua Tim Pemenangan</p>
                <div class="h-16"></div>
                <p class="font-extrabold text-slate-900 underline">( ____________________ )</p>
            </div>
            <div>
                <p class="font-bold text-slate-600">Disahkan Oleh,</p>
                <p class="font-bold text-slate-800 mt-0.5">Koordinator Quick Count TPS</p>
                <div class="h-16"></div>
                <p class="font-extrabold text-slate-900 underline">( ____________________ )</p>
            </div>
        </div>

    </div>

</body>
</html>
