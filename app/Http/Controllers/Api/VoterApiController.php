<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Http\Request;

class VoterApiController extends Controller
{
    /**
     * List voters with pagination, search, and TPS filter.
     */
    public function index(Request $request)
    {
        $selectedTps = $request->query('tps_id');
        $search      = $request->query('search');

        $voters = Voter::with('tps')
            ->when($selectedTps, fn($q) => $q->where('tps_id', $selectedTps))
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%"))
            ->orderBy('tps_id')
            ->orderBy('nama')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data'    => $voters,
        ]);
    }

    /**
     * Get detail of a single voter.
     */
    public function show(Voter $voter)
    {
        return response()->json([
            'success' => true,
            'data'    => $voter->load('tps'),
        ]);
    }

    /**
     * Add new voter manually via API.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tps_id'       => 'required|exists:tps,id',
            'nama'         => 'required|string|max:255',
            'is_supporter' => 'nullable|boolean',
        ], [
            'tps_id.required' => 'Pilih TPS terlebih dahulu.',
            'tps_id.exists'   => 'TPS tidak valid.',
            'nama.required'   => 'Nama pemilih wajib diisi.',
        ]);

        $voter = Voter::create([
            'tps_id'       => $request->tps_id,
            'nama'         => $request->nama,
            'is_supporter' => $request->has('is_supporter') ? (bool) $request->is_supporter : false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pemilih berhasil ditambahkan.',
            'data'    => $voter->load('tps'),
        ], 201);
    }

    /**
     * Update voter data via API.
     */
    public function update(Request $request, Voter $voter)
    {
        $request->validate([
            'tps_id'       => 'sometimes|required|exists:tps,id',
            'nama'         => 'sometimes|required|string|max:255',
            'is_supporter' => 'nullable|boolean',
        ], [
            'tps_id.required' => 'Pilih TPS terlebih dahulu.',
            'tps_id.exists'   => 'TPS tidak valid.',
            'nama.required'   => 'Nama pemilih wajib diisi.',
        ]);

        $voter->fill($request->only(['tps_id', 'nama']));

        if ($request->has('is_supporter')) {
            $voter->is_supporter = (bool) $request->is_supporter;
        }

        $voter->save();

        return response()->json([
            'success' => true,
            'message' => 'Data pemilih berhasil diperbarui.',
            'data'    => $voter->fresh('tps'),
        ]);
    }

    /**
     * Delete a single voter via API.
     */
    public function destroy(Voter $voter)
    {
        $voter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pemilih berhasil dihapus.',
        ]);
    }

    /**
     * Toggle voter status (is_supporter) via API.
     */
    public function toggleSupporter(Voter $voter)
    {
        $voter->update(['is_supporter' => ! $voter->is_supporter]);

        return response()->json([
            'success'      => true,
            'is_supporter' => $voter->is_supporter,
            'message'      => $voter->is_supporter
                ? "{$voter->nama} ditandai sebagai Pendukung."
                : "{$voter->nama} bukan lagi Pendukung.",
            'data'         => $voter->fresh('tps'),
        ]);
    }

    /**
     * Bulk update supporters status.
     */
    public function bulkSupporter(Request $request)
    {
        $request->validate([
            'voter_ids'    => 'required|array',
            'voter_ids.*'  => 'exists:voters,id',
            'is_supporter' => 'required|boolean',
        ]);

        $voterIds    = $request->voter_ids;
        $isSupporter = (bool) $request->is_supporter;

        Voter::whereIn('id', $voterIds)->update(['is_supporter' => $isSupporter]);

        $count = count($voterIds);
        $statusText = $isSupporter ? 'Pendukung' : 'Pemilih Biasa';

        return response()->json([
            'success'      => true,
            'is_supporter' => $isSupporter,
            'updated_count'=> $count,
            'message'      => "Berhasil memperbarui {$count} pemilih menjadi {$statusText}.",
        ]);
    }

    /**
     * Bulk delete voters.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'voter_ids'   => 'required|array',
            'voter_ids.*' => 'exists:voters,id',
        ], [
            'voter_ids.required' => 'Pilih minimal satu pemilih untuk dihapus.',
            'voter_ids.*.exists' => 'Salah satu pemilih tidak ditemukan.',
        ]);

        $voterIds = $request->voter_ids;
        $count    = count($voterIds);

        Voter::whereIn('id', $voterIds)->delete();

        return response()->json([
            'success'       => true,
            'deleted_count' => $count,
            'message'       => "Berhasil menghapus {$count} data pemilih.",
        ]);
    }

    /**
     * Dedicated Supporters List endpoint.
     */
    public function supporters(Request $request)
    {
        $selectedTps = $request->query('tps_id');
        $search      = $request->query('search');

        $supporters = Voter::with('tps')
            ->where('is_supporter', true)
            ->when($selectedTps, fn($q) => $q->where('tps_id', $selectedTps))
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%"))
            ->orderBy('tps_id')
            ->orderBy('nama')
            ->paginate(30);

        return response()->json([
            'success'          => true,
            'total_supporters' => Voter::where('is_supporter', true)->count(),
            'data'             => $supporters,
        ]);
    }

    /**
     * Laporan / Rekapitulasi per TPS endpoint.
     */
    public function laporan()
    {
        $rekap = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
            'voters as non_supporters_count' => fn($q) => $q->where('is_supporter', false),
        ])->orderBy('nama_tps')->get()->map(function ($tps) {
            $pct = $tps->voters_count > 0 ? round(($tps->supporters_count / $tps->voters_count) * 100, 1) : 0;
            return [
                'id'                   => $tps->id,
                'nama_tps'             => $tps->nama_tps,
                'total_voters'         => $tps->voters_count,
                'total_supporters'     => $tps->supporters_count,
                'total_non_supporters' => $tps->non_supporters_count,
                'percentage'           => $pct,
                'status'               => $pct >= 50 ? 'Unggul' : ($pct >= 30 ? 'Potensial' : 'Perlu Ditingkatkan'),
            ];
        });

        $totalVoters     = Voter::count();
        $totalSupporters = Voter::where('is_supporter', true)->count();
        $overallPercentage = $totalVoters > 0 ? round(($totalSupporters / $totalVoters) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'summary' => [
                'total_tps'          => Tps::count(),
                'total_voters'       => $totalVoters,
                'total_supporters'   => $totalSupporters,
                'overall_percentage' => $overallPercentage,
            ],
            'rekap_per_tps' => $rekap,
        ]);
    }
}

