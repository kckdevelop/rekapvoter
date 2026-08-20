<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Pendukung Kades</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-b: 2px solid #0f172a; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 4px 0 0; color: #64748b; font-size: 11px; }
        table { w-full; width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
        th { bg-slate-100; background-color: #f1f5f9; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #16a34a; color: white; border: none; border-radius: 6px; cursor: pointer;">
            🖨️ Cetak Dokumen Ini
        </button>
    </div>

    <div class="header">
        <h1>Laporan Rekapitulasi Pendukung Calon Kepala Desa</h1>
        <p>Data Rekapitulasi per Tempat Pemungutan Suara (TPS)</p>
        <p>Tanggal Cetak: {{ date('d F Y, H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="40" class="text-center">No</th>
                <th>Nama TPS</th>
                <th class="text-center">Total Pemilih (DPT)</th>
                <th class="text-center">Jumlah Pendukung</th>
                <th class="text-center">Belum Pendukung</th>
                <th class="text-center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapTps as $tps)
            @php
                $pct = $tps->voters_count > 0 ? round(($tps->supporters_count / $tps->voters_count) * 100, 1) : 0;
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="font-bold">{{ $tps->nama_tps }}</td>
                <td class="text-center">{{ number_format($tps->voters_count) }}</td>
                <td class="text-center font-bold" style="color: #15803d;">{{ number_format($tps->supporters_count) }}</td>
                <td class="text-center">{{ number_format($tps->non_supporters_count) }}</td>
                <td class="text-center font-bold">{{ $pct }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="2">TOTAL KESELURUHAN</td>
                <td class="text-center">{{ number_format($totalVoters) }}</td>
                <td class="text-center" style="color: #15803d;">{{ number_format($totalSupporters) }}</td>
                <td class="text-center">{{ number_format($totalVoters - $totalSupporters) }}</td>
                <td class="text-center">{{ $overallPercentage }}%</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 40px; text-align: right;">
        <p>Dicetak oleh: <strong>{{ Auth::user()->name }}</strong></p>
        <br><br><br>
        <p>( _______________________ )</p>
    </div>

</body>
</html>
