<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Tps;
use App\Models\TpsQuickCandidateResult;
use Illuminate\Http\Request;

class RekapQuickController extends Controller
{
    /**
     * Halaman Rekapitulasi Perbandingan Hasil Quick Count vs Data Pendukung per TPS
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Auto-seed kandidat jika belum ada
        if (Candidate::count() === 0) {
            Candidate::create(['nomor_urut' => 1, 'nama' => 'Nurma Setiawan, SE', 'is_main_candidate' => true, 'warna_badge' => '#059669']);
            Candidate::create(['nomor_urut' => 2, 'nama' => 'Drs. H. Subagyo, M.Si', 'is_main_candidate' => false, 'warna_badge' => '#2563eb']);
        }

        $candidates = Candidate::orderBy('nomor_urut')->get();

        $query = Tps::with([
            'quickCandidateResults.candidate'
        ])->withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ]);

        if ($user->isSaksi() && $user->tps_id) {
            $query->where('id', $user->tps_id);
        }

        $rekapTps = $query->orderBy('nama_tps')->get();

        // Total Suara Quick per Kandidat secara Individual
        $candidateTotals = TpsQuickCandidateResult::selectRaw('candidate_id, SUM(jumlah_suara) as total_suara')
            ->groupBy('candidate_id')
            ->pluck('total_suara', 'candidate_id');

        $totalTps            = $rekapTps->count();
        $tpsSubmittedCount   = $rekapTps->where('quick_is_submitted', true)->count();
        $totalDpt            = $rekapTps->sum('voters_count');
        $totalSupporters     = $rekapTps->sum('supporters_count');
        $totalSuaraKandidat  = $rekapTps->sum('quick_suara_kandidat');
        $totalSuaraLawan     = $rekapTps->sum('quick_suara_lawan');
        $totalSuaraTidakSah  = $rekapTps->sum('quick_suara_tidak_sah');
        $totalSuaraSah       = $totalSuaraKandidat + $totalSuaraLawan;
        $totalSuaraMasuk     = $totalSuaraSah + $totalSuaraTidakSah;

        // Persentase Suara Quick vs Sah
        $persentaseSuaraKandidat = $totalSuaraSah > 0 ? round(($totalSuaraKandidat / $totalSuaraSah) * 100, 1) : 0;
        
        // Persentase Konversi Pendukung ke Suara Quick (Suara Quick / Total Pendukung * 100%)
        $persentaseKonversi = $totalSupporters > 0 ? round(($totalSuaraKandidat / $totalSupporters) * 100, 1) : ($totalSuaraKandidat > 0 ? 100 : 0);

        // Total Selisih (Suara Quick Kandidat - Target Pendukung)
        $selisihKandidatVsPendukung = $totalSuaraKandidat - $totalSupporters;

        // Status Keberhasilan Overall
        if ($totalSupporters == 0) {
            $statusKeberhasilanOverall = $totalSuaraKandidat > 0 ? '🚀 Surplus (Tanpa Target)' : '⚪ Belum Ada Target';
            $badgeColorOverall = 'emerald';
        } elseif ($persentaseKonversi > 100) {
            $statusKeberhasilanOverall = "🔥 Melampaui Target ({$persentaseKonversi}%)";
            $badgeColorOverall = 'emerald';
        } elseif ($persentaseKonversi == 100) {
            $statusKeberhasilanOverall = "✅ Target 100% Tercapai";
            $badgeColorOverall = 'teal';
        } elseif ($persentaseKonversi >= 75) {
            $statusKeberhasilanOverall = "⚡ Capaian High ({$persentaseKonversi}%)";
            $badgeColorOverall = 'amber';
        } else {
            $statusKeberhasilanOverall = "📌 Belum Tercapai ({$persentaseKonversi}%)";
            $badgeColorOverall = 'rose';
        }

        return view('rekapquick.index', compact(
            'candidates',
            'candidateTotals',
            'rekapTps',
            'totalTps',
            'tpsSubmittedCount',
            'totalDpt',
            'totalSupporters',
            'totalSuaraKandidat',
            'totalSuaraLawan',
            'totalSuaraTidakSah',
            'totalSuaraSah',
            'totalSuaraMasuk',
            'persentaseSuaraKandidat',
            'persentaseKonversi',
            'selisihKandidatVsPendukung',
            'statusKeberhasilanOverall',
            'badgeColorOverall',
            'user'
        ));
    }

    /**
     * Cetak Laporan Rekapitulasi Quick Count vs Pendukung
     */
    public function print(Request $request)
    {
        $user = $request->user();
        $candidates = Candidate::orderBy('nomor_urut')->get();

        $query = Tps::with([
            'quickCandidateResults.candidate'
        ])->withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ]);

        if ($user->isSaksi() && $user->tps_id) {
            $query->where('id', $user->tps_id);
        }

        $rekapTps = $query->orderBy('nama_tps')->get();

        $totalDpt            = $rekapTps->sum('voters_count');
        $totalSupporters     = $rekapTps->sum('supporters_count');
        $totalSuaraKandidat  = $rekapTps->sum('quick_suara_kandidat');
        $totalSuaraLawan     = $rekapTps->sum('quick_suara_lawan');
        $totalSuaraTidakSah  = $rekapTps->sum('quick_suara_tidak_sah');
        $totalSuaraSah       = $totalSuaraKandidat + $totalSuaraLawan;
        $persentaseKemenangan = $totalSuaraSah > 0 ? round(($totalSuaraKandidat / $totalSuaraSah) * 100, 1) : 0;
        $persentaseKonversi   = $totalSupporters > 0 ? round(($totalSuaraKandidat / $totalSupporters) * 100, 1) : 0;

        return view('rekapquick.print', compact(
            'candidates',
            'rekapTps',
            'totalDpt',
            'totalSupporters',
            'totalSuaraKandidat',
            'totalSuaraLawan',
            'totalSuaraTidakSah',
            'totalSuaraSah',
            'persentaseKemenangan',
            'persentaseKonversi'
        ));
    }
}
