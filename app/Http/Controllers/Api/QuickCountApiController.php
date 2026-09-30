<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Tps;
use App\Models\TpsQuickCandidateResult;
use App\Models\Voter;
use Illuminate\Http\Request;

class QuickCountApiController extends Controller
{
    /**
     * Rekapitulasi Quick Count: perolehan suara per TPS vs target pendukung.
     * GET /api/quickcount
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $candidates = Candidate::orderBy('nomor_urut')->get();
        $mainCandidate = $candidates->firstWhere('is_main_candidate', true);

        $query = Tps::with(['quickCandidateResults.candidate'])
            ->withCount([
                'voters',
                'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
            ]);

        // Jika saksi login, batasi ke TPS miliknya
        if ($user && $user->isSaksi() && $user->tps_id) {
            $query->where('id', $user->tps_id);
        }

        $rekapTps = $query->orderBy('nama_tps')
            ->get()
            ->map(function ($tps) use ($candidates, $mainCandidate) {
                $suaraReal       = $tps->quick_suara_kandidat ?? 0;
                $targetPendukung = $tps->supporters_count ?? 0;
                $selisih         = $suaraReal - $targetPendukung;
                $pctKonversi     = $targetPendukung > 0
                    ? round(($suaraReal / $targetPendukung) * 100, 1)
                    : ($suaraReal > 0 ? 100 : 0);

                // Max opponent vote for victory status
                $maxOpponentVote = 0;
                $perolehanPerKandidat = [];
                foreach ($candidates as $cand) {
                    $res   = $tps->quickCandidateResults->firstWhere('candidate_id', $cand->id);
                    $votes = $res ? $res->jumlah_suara : 0;
                    $perolehanPerKandidat[] = [
                        'candidate_id'      => $cand->id,
                        'nomor_urut'        => $cand->nomor_urut,
                        'nama'              => $cand->nama,
                        'is_main_candidate' => $cand->is_main_candidate,
                        'jumlah_suara'      => $votes,
                    ];
                    if (!$cand->is_main_candidate && $votes > $maxOpponentVote) {
                        $maxOpponentVote = $votes;
                    }
                }

                $suaraSah   = $tps->quick_suara_sah ?? ($suaraReal + ($tps->quick_suara_lawan ?? 0));
                $pctSuaraSah = $suaraSah > 0 ? round(($suaraReal / $suaraSah) * 100, 1) : 0;

                // Status keberhasilan target
                if (!$tps->quick_is_submitted) {
                    $statusTarget = 'belum_input';
                } elseif ($targetPendukung === 0) {
                    $statusTarget = $suaraReal > 0 ? 'surplus_tanpa_target' : 'belum_ada_target';
                } elseif ($pctKonversi > 100) {
                    $statusTarget = 'melampaui';
                } elseif ($pctKonversi === 100.0) {
                    $statusTarget = 'tercapai';
                } elseif ($pctKonversi >= 75) {
                    $statusTarget = 'high';
                } else {
                    $statusTarget = 'belum_tercapai';
                }

                // Status kemenangan
                if (!$tps->quick_is_submitted) {
                    $statusKemenangan = 'belum_input';
                } elseif ($suaraReal > $maxOpponentVote) {
                    $statusKemenangan = 'menang';
                } elseif ($suaraReal === $maxOpponentVote) {
                    $statusKemenangan = 'seri';
                } else {
                    $statusKemenangan = 'kalah';
                }

                return [
                    'id'                     => $tps->id,
                    'nama_tps'               => $tps->nama_tps,
                    'total_dpt'              => $tps->voters_count,
                    'target_pendukung'       => $targetPendukung,
                    'suara_quick_kandidat'   => $suaraReal,
                    'suara_lawan_total'      => $tps->quick_suara_lawan ?? 0,
                    'suara_tidak_sah'        => $tps->quick_suara_tidak_sah ?? 0,
                    'suara_sah'              => $tps->quick_suara_sah ?? 0,
                    'selisih_real_vs_target' => $selisih,
                    'pct_konversi_target'    => $pctKonversi,
                    'pct_suara_sah'          => $pctSuaraSah,
                    'is_submitted'           => $tps->quick_is_submitted,
                    'waktu_input'            => $tps->quick_waktu_input?->format('Y-m-d H:i:s'),
                    'catatan_saksi'          => $tps->quick_catatan_saksi,
                    'status_target'          => $statusTarget,
                    'status_kemenangan'      => $statusKemenangan,
                    'perolehan_per_kandidat' => $perolehanPerKandidat,
                ];
            });

        // Global summary
        $totalSuaraKandidat  = $rekapTps->sum('suara_quick_kandidat');
        $totalSuaraLawan     = $rekapTps->sum('suara_lawan_total');
        $totalSuaraTidakSah  = $rekapTps->sum('suara_tidak_sah');
        $totalSuaraSah       = $totalSuaraKandidat + $totalSuaraLawan;
        $totalSupporters     = $rekapTps->sum('target_pendukung');
        $totalDpt            = $rekapTps->sum('total_dpt');
        $tpsSubmitted        = $rekapTps->where('is_submitted', true)->count();
        $totalTps            = $rekapTps->count();

        $pctKemenangan = $totalSuaraSah > 0
            ? round(($totalSuaraKandidat / $totalSuaraSah) * 100, 1)
            : 0;
        $pctKonversi = $totalSupporters > 0
            ? round(($totalSuaraKandidat / $totalSupporters) * 100, 1)
            : ($totalSuaraKandidat > 0 ? 100 : 0);
        $selisihTotal = $totalSuaraKandidat - $totalSupporters;

        return response()->json([
            'success' => true,
            'summary' => [
                'total_tps'               => $totalTps,
                'tps_submitted'           => $tpsSubmitted,
                'total_dpt'               => $totalDpt,
                'total_pendukung'         => $totalSupporters,
                'total_suara_kandidat'    => $totalSuaraKandidat,
                'total_suara_lawan'       => $totalSuaraLawan,
                'total_suara_tidak_sah'   => $totalSuaraTidakSah,
                'total_suara_sah'         => $totalSuaraSah,
                'pct_kemenangan'          => $pctKemenangan,
                'pct_konversi_target'     => $pctKonversi,
                'selisih_real_vs_target'  => $selisihTotal,
                'kandidat_utama'          => $mainCandidate ? [
                    'id'         => $mainCandidate->id,
                    'nama'       => $mainCandidate->nama,
                    'nomor_urut' => $mainCandidate->nomor_urut,
                ] : null,
            ],
            'rekap_per_tps' => $rekapTps->values(),
        ]);
    }

    /**
     * Quick count detail for a single TPS.
     * GET /api/quickcount/{tps}
     */
    public function show(Request $request, Tps $tps)
    {
        $user = $request->user();
        if ($user && $user->isSaksi() && $user->tps_id !== $tps->id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda hanya berhak mengakses TPS Anda sendiri.',
            ], 403);
        }

        $candidates = Candidate::orderBy('nomor_urut')->get();

        $tps->loadCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ])->load('quickCandidateResults.candidate');

        $suaraReal       = $tps->quick_suara_kandidat ?? 0;
        $targetPendukung = $tps->supporters_count ?? 0;
        $selisih         = $suaraReal - $targetPendukung;
        $pctKonversi     = $targetPendukung > 0
            ? round(($suaraReal / $targetPendukung) * 100, 1)
            : ($suaraReal > 0 ? 100 : 0);

        $perolehanPerKandidat = [];
        $maxOpponentVote = 0;
        foreach ($candidates as $cand) {
            $res   = $tps->quickCandidateResults->firstWhere('candidate_id', $cand->id);
            $votes = $res ? $res->jumlah_suara : 0;
            $perolehanPerKandidat[] = [
                'candidate_id'      => $cand->id,
                'nomor_urut'        => $cand->nomor_urut,
                'nama'              => $cand->nama,
                'is_main_candidate' => $cand->is_main_candidate,
                'jumlah_suara'      => $votes,
            ];
            if (!$cand->is_main_candidate && $votes > $maxOpponentVote) {
                $maxOpponentVote = $votes;
            }
        }

        $pctSuaraSah = ($tps->quick_suara_sah ?? 0) > 0
            ? round(($suaraReal / $tps->quick_suara_sah) * 100, 1)
            : 0;

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                     => $tps->id,
                'nama_tps'               => $tps->nama_tps,
                'total_dpt'              => $tps->voters_count,
                'target_pendukung'       => $targetPendukung,
                'suara_quick_kandidat'   => $suaraReal,
                'suara_lawan_total'      => $tps->quick_suara_lawan ?? 0,
                'suara_tidak_sah'        => $tps->quick_suara_tidak_sah ?? 0,
                'suara_sah'              => $tps->quick_suara_sah ?? 0,
                'selisih_real_vs_target' => $selisih,
                'pct_konversi_target'    => $pctKonversi,
                'pct_suara_sah'          => $pctSuaraSah,
                'is_submitted'           => $tps->quick_is_submitted,
                'waktu_input'            => $tps->quick_waktu_input?->format('Y-m-d H:i:s'),
                'catatan_saksi'          => $tps->quick_catatan_saksi,
                'perolehan_per_kandidat' => $perolehanPerKandidat,
            ],
        ]);
    }

    /**
     * Submit / update quick count results for a TPS.
     * POST /api/quickcount/{tps}
     */
    public function submit(Request $request, Tps $tps)
    {
        $user = $request->user();
        if ($user && $user->isSaksi() && $user->tps_id !== $tps->id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda hanya dapat menginput Quick Count untuk TPS yang ditugaskan kepada Anda.',
            ], 403);
        }

        $request->validate([
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
                TpsQuickCandidateResult::updateOrCreate(
                    ['tps_id' => $tps->id, 'candidate_id' => $candidate->id],
                    ['jumlah_suara' => $suara]
                );

                if ($candidate->is_main_candidate) {
                    $suaraKandidatUtama = $suara;
                } else {
                    $suaraLawanTotal += $suara;
                }
            }
        }

        $tps->update([
            'quick_suara_kandidat'   => $suaraKandidatUtama,
            'quick_suara_lawan'      => $suaraLawanTotal,
            'quick_suara_tidak_sah'  => (int) ($request->suara_tidak_sah ?? 0),
            'quick_catatan_saksi'    => $request->catatan_saksi,
            'quick_is_submitted'     => true,
            'quick_waktu_input'      => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Hasil Quick Count untuk {$tps->nama_tps} berhasil disimpan.",
            'data'    => [
                'tps_id'             => $tps->id,
                'nama_tps'           => $tps->nama_tps,
                'suara_kandidat'     => $suaraKandidatUtama,
                'suara_lawan'        => $suaraLawanTotal,
                'suara_tidak_sah'    => (int) ($request->suara_tidak_sah ?? 0),
                'suara_sah'          => $suaraKandidatUtama + $suaraLawanTotal,
                'is_submitted'       => true,
                'waktu_input'        => now()->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Aggregate summary of quick count vs supporter target.
     * GET /api/quickcount/summary
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        $candidates = Candidate::orderBy('nomor_urut')->get();
        $mainCandidate = $candidates->firstWhere('is_main_candidate', true);

        $queryResult = TpsQuickCandidateResult::selectRaw('candidate_id, SUM(jumlah_suara) as total_suara');
        if ($user && $user->isSaksi() && $user->tps_id) {
            $queryResult->where('tps_id', $user->tps_id);
        }

        $candidateTotals = $queryResult->groupBy('candidate_id')
            ->pluck('total_suara', 'candidate_id');

        $totalSuaraKandidat = $mainCandidate ? ($candidateTotals[$mainCandidate->id] ?? 0) : 0;

        $tpsQuery = Tps::withCount([
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ]);

        if ($user && $user->isSaksi() && $user->tps_id) {
            $tpsQuery->where('id', $user->tps_id);
        }

        $allTps = $tpsQuery->get();

        $totalTps           = $allTps->count();
        $tpsSubmitted       = $allTps->where('quick_is_submitted', true)->count();
        $totalSupporters    = $allTps->sum('supporters_count');
        $totalSuaraLawan    = $allTps->sum('quick_suara_lawan');
        $totalSuaraTidakSah = $allTps->sum('quick_suara_tidak_sah');
        $totalSuaraSah      = $totalSuaraKandidat + $totalSuaraLawan;

        $voterQuery = Voter::query();
        if ($user && $user->isSaksi() && $user->tps_id) {
            $voterQuery->where('tps_id', $user->tps_id);
        }
        $totalDpt = $voterQuery->count();

        $pctKemenangan  = $totalSuaraSah > 0 ? round(($totalSuaraKandidat / $totalSuaraSah) * 100, 1) : 0;
        $pctKonversi    = $totalSupporters > 0
            ? round(($totalSuaraKandidat / $totalSupporters) * 100, 1)
            : ($totalSuaraKandidat > 0 ? 100 : 0);
        $selisih        = $totalSuaraKandidat - $totalSupporters;

        $candidateSummary = $candidates->map(function ($c) use ($candidateTotals) {
            return [
                'id'                => $c->id,
                'nomor_urut'        => $c->nomor_urut,
                'nama'              => $c->nama,
                'is_main_candidate' => $c->is_main_candidate,
                'total_suara'       => (int) ($candidateTotals[$c->id] ?? 0),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'total_tps'              => $totalTps,
                'tps_submitted'          => $tpsSubmitted,
                'total_dpt'              => $totalDpt,
                'total_pendukung'        => $totalSupporters,
                'total_suara_sah'        => $totalSuaraSah,
                'total_suara_tidak_sah'  => $totalSuaraTidakSah,
                'pct_kemenangan'         => $pctKemenangan,
                'pct_konversi_target'    => $pctKonversi,
                'selisih_real_vs_target' => $selisih,
                'perolehan_per_kandidat' => $candidateSummary,
            ],
        ]);
    }

    /**
     * Reset / Hapus Hasil Quick Count sebuah TPS via API.
     * DELETE /api/quickcount/{tps}/reset atau POST /api/quickcount/{tps}/reset
     */
    public function reset(Request $request, Tps $tps)
    {
        $user = $request->user();
        if ($user && $user->isSaksi() && $user->tps_id !== $tps->id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda hanya berhak mereset data TPS yang ditugaskan kepada Anda.',
            ], 403);
        }

        TpsQuickCandidateResult::where('tps_id', $tps->id)->delete();

        $tps->update([
            'quick_suara_kandidat'  => 0,
            'quick_suara_lawan'     => 0,
            'quick_suara_tidak_sah' => 0,
            'quick_catatan_saksi'   => null,
            'quick_is_submitted'    => false,
            'quick_waktu_input'     => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Data Quick Count untuk {$tps->nama_tps} berhasil direset.",
        ]);
    }
}
