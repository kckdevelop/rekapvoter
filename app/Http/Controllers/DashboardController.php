<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tpsQuery = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ]);

        if ($user->isSaksi() && $user->tps_id) {
            $tpsQuery->where('id', $user->tps_id);
        }

        $tpsData = $tpsQuery->orderBy('nama_tps')->get();

        $voterQuery = Voter::query();
        $suppQuery  = Voter::where('is_supporter', true);

        if ($user->isSaksi() && $user->tps_id) {
            $voterQuery->where('tps_id', $user->tps_id);
            $suppQuery->where('tps_id', $user->tps_id);
        }

        $totalTps        = $tpsData->count();
        $totalVoters     = $voterQuery->count();
        $totalSupporters = $suppQuery->count();

        $chartLabels     = $tpsData->pluck('nama_tps');
        $chartVoters     = $tpsData->pluck('voters_count');
        $chartSupporters = $tpsData->pluck('supporters_count');

        // Quick & Real stats for dashboard overview cards
        $totalQuickSubmitted = $tpsData->where('quick_is_submitted', true)->count();
        $totalRealSubmitted  = $tpsData->where('is_submitted', true)->count();

        // === Quick Count Pie Chart Data ===
        $candidates = Candidate::orderBy('nomor_urut')->get();

        // Hitung total suara quick count per kandidat dari tps_quick_candidate_results
        $quickCandidateTotals = \App\Models\TpsQuickCandidateResult::selectRaw('candidate_id, SUM(jumlah_suara) as total_suara')
            ->groupBy('candidate_id')
            ->pluck('total_suara', 'candidate_id');

        // Jika belum ada data di tabel pivot, fallback ke kolom legacy di tps
        $quickSuaraKandidat = $quickCandidateTotals->get($candidates->firstWhere('is_main_candidate', true)?->id ?? 0)
            ?? $tpsData->sum('quick_suara_kandidat');
        $quickSuaraLawan    = $tpsData->sum('quick_suara_lawan');
        $quickSuaraTidakSah = $tpsData->sum('quick_suara_tidak_sah');
        $quickSuaraSah      = $quickSuaraKandidat + $quickSuaraLawan;
        $quickSuaraMasuk    = $quickSuaraSah + $quickSuaraTidakSah;

        // Data untuk pie chart: label & suara per kandidat + tidak sah
        $quickPieLabels = [];
        $quickPieData   = [];
        $quickPieColors = [];

        // Warna default dan warna fallback per kandidat
        $defaultColors = ['#6366f1', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#06b6d4'];

        foreach ($candidates as $idx => $candidate) {
            $suara = $quickCandidateTotals->get($candidate->id, 0);
            // Fallback bila data pivot kosong: gunakan kolom legacy
            if ($suara == 0 && $candidates->count() <= 2) {
                $suara = $candidate->is_main_candidate
                    ? $tpsData->sum('quick_suara_kandidat')
                    : $tpsData->sum('quick_suara_lawan');
            }
            $quickPieLabels[] = "No. {$candidate->nomor_urut} — {$candidate->nama}";
            $quickPieData[]   = (int) $suara;
            $quickPieColors[] = $candidate->warna_badge ?? $defaultColors[$idx % count($defaultColors)];
        }


        $quickPersentaseKandidat = $quickSuaraSah > 0
            ? round(($quickSuaraKandidat / $quickSuaraSah) * 100, 1)
            : 0;

        return view('dashboard', compact(
            'totalTps',
            'totalVoters',
            'totalSupporters',
            'chartLabels',
            'chartVoters',
            'chartSupporters',
            'totalQuickSubmitted',
            'totalRealSubmitted',
            'candidates',
            'quickPieLabels',
            'quickPieData',
            'quickPieColors',
            'quickSuaraKandidat',
            'quickSuaraLawan',
            'quickSuaraTidakSah',
            'quickSuaraSah',
            'quickSuaraMasuk',
            'quickPersentaseKandidat',
            'user'
        ));
    }
}

