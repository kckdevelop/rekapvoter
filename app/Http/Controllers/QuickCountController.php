<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Tps;
use App\Models\TpsQuickCandidateResult;
use Illuminate\Http\Request;

class QuickCountController extends Controller
{
    /**
     * Halaman Input Hasil Quick Count per TPS
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
        $search     = $request->query('search');

        $query = Tps::with([
            'quickCandidateResults.candidate'
        ])->withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ]);

        // Jika user adalah saksi TPS, batasi hanya ke TPS miliknya
        if ($user->isSaksi() && $user->tps_id) {
            $query->where('id', $user->tps_id);
        } elseif (!empty($search)) {
            $query->where('nama_tps', 'like', "%{$search}%");
        }

        $tpsList = $query->orderBy('nama_tps')->paginate(15)->withQueryString();

        // Total Suara Quick Count per Kandidat secara Individual
        $candidateTotals = TpsQuickCandidateResult::selectRaw('candidate_id, SUM(jumlah_suara) as total_suara')
            ->groupBy('candidate_id')
            ->pluck('total_suara', 'candidate_id');

        // Main Candidate (Kandidat Utama)
        $mainCandidate = $candidates->firstWhere('is_main_candidate', true);
        $totalSuaraKandidat = $mainCandidate ? ($candidateTotals[$mainCandidate->id] ?? 0) : 0;

        // Opponent Candidates
        $opponents = $candidates->where('is_main_candidate', false);
        $totalSuaraLawan = 0;
        $maxOpponentVote = 0;
        $topOpponent     = null;

        foreach ($opponents as $opp) {
            $oppVote = $candidateTotals[$opp->id] ?? 0;
            $totalSuaraLawan += $oppVote;
            if ($oppVote >= $maxOpponentVote) {
                $maxOpponentVote = $oppVote;
                $topOpponent     = $opp;
            }
        }

        // Ringkasan Statistik Quick Count
        $allTps = Tps::all();
        $totalTpsCount      = $allTps->count();
        $tpsSubmittedCount  = $allTps->where('quick_is_submitted', true)->count();
        $totalSuaraTidakSah = $allTps->sum('quick_suara_tidak_sah');
        $totalSuaraSah      = $totalSuaraKandidat + $totalSuaraLawan;
        $totalSuaraMasuk    = $totalSuaraSah + $totalSuaraTidakSah;

        // Persentase Suara Quick Count
        $persentaseKemenangan = $totalSuaraSah > 0 ? round(($totalSuaraKandidat / $totalSuaraSah) * 100, 1) : 0;
        $persentaseLawan      = $totalSuaraSah > 0 ? round(($totalSuaraLawan / $totalSuaraSah) * 100, 1) : 0;

        // Selisih Suara
        $selisihSuaraUtamaVsTopOpponent = $totalSuaraKandidat - $maxOpponentVote;
        $selisihSuaraUtamaVsTotalLawan  = $totalSuaraKandidat - $totalSuaraLawan;

        // Penentuan Status Kemenangan Quick Count
        if ($tpsSubmittedCount == 0 || $totalSuaraSah == 0) {
            $statusKemenangan  = 'BELUM ADA DATA';
            $statusType        = 'no_data';
            $statusBadgeText   = '⏳ Belum Ada Data Quick Count';
            $statusSummary     = 'Menunggu input perolehan suara quick count dari TPS.';
        } elseif ($totalSuaraKandidat > $maxOpponentVote) {
            $statusKemenangan  = 'UNGGUL (MENANG)';
            $statusType        = 'winning';
            $statusBadgeText   = '🏆 UNGGUL (QUICK COUNT)';
            $topOpponentName   = $topOpponent ? $topOpponent->nama : 'Kandidat Lawan';
            $statusSummary     = "Kandidat Utama ({$mainCandidate->nama}) unggul +" . number_format($selisihSuaraUtamaVsTopOpponent) . " suara ({$persentaseKemenangan}%) dibanding lawan terkuat ({$topOpponentName}).";
        } elseif ($totalSuaraKandidat == $maxOpponentVote) {
            $statusKemenangan  = 'SERI / IMBANG';
            $statusType        = 'tied';
            $statusBadgeText   = '⚖️ SERI / IMBANG';
            $statusSummary     = "Perolehan suara quick count kandidat utama seimbang dengan lawan terkuat pada " . number_format($totalSuaraKandidat) . " suara ({$persentaseKemenangan}%).";
        } else {
            $statusKemenangan  = 'TERTINGGAL (KALAH)';
            $statusType        = 'trailing';
            $statusBadgeText   = '⚠️ TERTINGGAL (QUICK COUNT)';
            $topOpponentName   = $topOpponent ? $topOpponent->nama : 'Kandidat Lawan';
            $statusSummary     = "Kandidat Utama ({$mainCandidate->nama}) tertinggal " . number_format(abs($selisihSuaraUtamaVsTopOpponent)) . " suara ({$persentaseKemenangan}%) di belakang {$topOpponentName}.";
        }

        return view('quickcount.index', compact(
            'candidates',
            'candidateTotals',
            'tpsList',
            'search',
            'totalTpsCount',
            'tpsSubmittedCount',
            'totalSuaraKandidat',
            'totalSuaraLawan',
            'totalSuaraTidakSah',
            'totalSuaraSah',
            'totalSuaraMasuk',
            'persentaseKemenangan',
            'persentaseLawan',
            'mainCandidate',
            'topOpponent',
            'maxOpponentVote',
            'selisihSuaraUtamaVsTopOpponent',
            'selisihSuaraUtamaVsTotalLawan',
            'statusKemenangan',
            'statusType',
            'statusBadgeText',
            'statusSummary',
            'user'
        ));
    }

    /**
     * Update/Simpan Hasil Suara Quick Count per TPS
     */
    public function update(Request $request, Tps $tp)
    {
        $user = $request->user();

        // Cek otorisasi saksi
        if ($user->isSaksi() && $user->tps_id !== $tp->id) {
            return redirect()->back()->with('error', 'Anda hanya berhak mengelola data TPS yang ditugaskan kepada Anda.');
        }

        $validated = $request->validate([
            'votes'           => 'required|array',
            'votes.*'         => 'required|integer|min:0',
            'suara_tidak_sah' => 'nullable|integer|min:0',
            'catatan_saksi'   => 'nullable|string|max:500',
        ], [
            'votes.required'   => 'Data perolehan suara per calon wajib diisi.',
            'votes.*.required' => 'Jumlah suara setiap calon wajib diisi.',
            'votes.*.min'      => 'Jumlah suara minimal 0.',
        ]);

        $candidates = Candidate::all()->keyBy('id');
        $suaraKandidatUtama = 0;
        $suaraLawanTotal    = 0;

        foreach ($request->input('votes', []) as $candidateId => $jumlahSuara) {
            $candidate = $candidates->get($candidateId);
            $suara     = (int) $jumlahSuara;

            if ($candidate) {
                // Simpan/Update perolehan suara per kandidat di tabel quick count
                TpsQuickCandidateResult::updateOrCreate(
                    [
                        'tps_id'       => $tp->id,
                        'candidate_id' => $candidate->id,
                    ],
                    [
                        'jumlah_suara' => $suara,
                    ]
                );

                if ($candidate->is_main_candidate) {
                    $suaraKandidatUtama = $suara;
                } else {
                    $suaraLawanTotal += $suara;
                }
            }
        }

        // Update di tabel TPS untuk quick count
        $tp->update([
            'quick_suara_kandidat'   => $suaraKandidatUtama,
            'quick_suara_lawan'      => $suaraLawanTotal,
            'quick_suara_tidak_sah'  => (int) ($validated['suara_tidak_sah'] ?? 0),
            'quick_catatan_saksi'    => $validated['catatan_saksi'] ?? null,
            'quick_is_submitted'     => true,
            'quick_waktu_input'      => now(),
        ]);

        return redirect()->back()->with('success', "Hasil Quick Count untuk {$tp->nama_tps} berhasil disimpan.");
    }

    /**
     * Reset / Hapus Hasil Quick Count sebuah TPS
     * Admin dapat mereset semua TPS, Saksi hanya bisa mereset TPS miliknya.
     */
    public function reset(Request $request, Tps $tp)
    {
        $user = $request->user();

        // Otorisasi: saksi hanya bisa reset TPS miliknya
        if ($user->isSaksi() && $user->tps_id !== $tp->id) {
            return redirect()->back()->with('error', 'Anda hanya berhak mereset data TPS yang ditugaskan kepada Anda.');
        }

        // Hapus data pivot quick count per kandidat
        TpsQuickCandidateResult::where('tps_id', $tp->id)->delete();

        // Reset kolom quick count di tabel tps
        $tp->update([
            'quick_suara_kandidat'  => 0,
            'quick_suara_lawan'     => 0,
            'quick_suara_tidak_sah' => 0,
            'quick_catatan_saksi'   => null,
            'quick_is_submitted'    => false,
            'quick_waktu_input'     => null,
        ]);

        return redirect()->back()->with('success', "Data Quick Count untuk {$tp->nama_tps} berhasil direset.");
    }
}

