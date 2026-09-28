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
        $user = $request->user();
        $selectedTps = $request->query('tps_id');
        $search      = $request->query('search');

        // Jika user adalah saksi, paksa filter hanya TPS miliknya
        if ($user && $user->isSaksi() && $user->tps_id) {
            $selectedTps = $user->tps_id;
        }

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
    public function show(Request $request, Voter $voter)
    {
        $user = $request->user();
        if ($user && $user->isSaksi() && $user->tps_id !== $voter->tps_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Pemilih ini bukan berada di TPS Anda.',
            ], 403);
        }

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
        $user = $request->user();

        // Jika saksi, kunci tps_id ke TPS saksi
        $targetTpsId = ($user && $user->isSaksi() && $user->tps_id) ? $user->tps_id : $request->tps_id;

        $request->merge(['tps_id' => $targetTpsId]);

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
            'tps_id'       => $targetTpsId,
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
        $user = $request->user();
        if ($user && $user->isSaksi() && $user->tps_id !== $voter->tps_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Pemilih ini bukan berada di TPS Anda.',
            ], 403);
        }

        $request->validate([
            'tps_id'       => 'sometimes|required|exists:tps,id',
            'nama'         => 'sometimes|required|string|max:255',
            'is_supporter' => 'nullable|boolean',
        ], [
            'tps_id.required' => 'Pilih TPS terlebih dahulu.',
            'tps_id.exists'   => 'TPS tidak valid.',
            'nama.required'   => 'Nama pemilih wajib diisi.',
        ]);

        if ($user && $user->isSaksi()) {
            $voter->nama = $request->input('nama', $voter->nama);
        } else {
            $voter->fill($request->only(['tps_id', 'nama']));
        }

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
    public function destroy(Request $request, Voter $voter)
    {
        $user = $request->user();
        if ($user && $user->isSaksi() && $user->tps_id !== $voter->tps_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Pemilih ini bukan berada di TPS Anda.',
            ], 403);
        }

        $voter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pemilih berhasil dihapus.',
        ]);
    }

    /**
     * Toggle voter status (is_supporter) via API.
     */
    public function toggleSupporter(Request $request, Voter $voter)
    {
        $user = $request->user();
        if ($user && $user->isSaksi() && $user->tps_id !== $voter->tps_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Pemilih ini bukan berada di TPS Anda.',
            ], 403);
        }

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
        $user = $request->user();
        $request->validate([
            'voter_ids'    => 'required|array',
            'voter_ids.*'  => 'exists:voters,id',
            'is_supporter' => 'required|boolean',
        ]);

        $query = Voter::whereIn('id', $request->voter_ids);
        if ($user && $user->isSaksi() && $user->tps_id) {
            $query->where('tps_id', $user->tps_id);
        }

        $isSupporter = (bool) $request->is_supporter;
        $count = $query->update(['is_supporter' => $isSupporter]);
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
        $user = $request->user();
        $request->validate([
            'voter_ids'   => 'required|array',
            'voter_ids.*' => 'exists:voters,id',
        ], [
            'voter_ids.required' => 'Pilih minimal satu pemilih untuk dihapus.',
            'voter_ids.*.exists' => 'Salah satu pemilih tidak ditemukan.',
        ]);

        $query = Voter::whereIn('id', $request->voter_ids);
        if ($user && $user->isSaksi() && $user->tps_id) {
            $query->where('tps_id', $user->tps_id);
        }

        $count = $query->delete();

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
        $user = $request->user();
        $selectedTps = $request->query('tps_id');
        $search      = $request->query('search');

        if ($user && $user->isSaksi() && $user->tps_id) {
            $selectedTps = $user->tps_id;
        }

        $supporters = Voter::with('tps')
            ->where('is_supporter', true)
            ->when($selectedTps, fn($q) => $q->where('tps_id', $selectedTps))
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%"))
            ->orderBy('tps_id')
            ->orderBy('nama')
            ->paginate(30);

        $totalSupportersQuery = Voter::where('is_supporter', true);
        if ($user && $user->isSaksi() && $user->tps_id) {
            $totalSupportersQuery->where('tps_id', $user->tps_id);
        }

        return response()->json([
            'success'          => true,
            'total_supporters' => $totalSupportersQuery->count(),
            'data'             => $supporters,
        ]);
    }

    /**
     * Laporan / Rekapitulasi per TPS endpoint.
     */
    public function laporan(Request $request)
    {
        $user = $request->user();
        $tpsQuery = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
            'voters as non_supporters_count' => fn($q) => $q->where('is_supporter', false),
        ]);

        if ($user && $user->isSaksi() && $user->tps_id) {
            $tpsQuery->where('id', $user->tps_id);
        }

        $rekap = $tpsQuery->orderBy('nama_tps')->get()->map(function ($tps) {
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

        $voterQuery = Voter::query();
        if ($user && $user->isSaksi() && $user->tps_id) {
            $voterQuery->where('tps_id', $user->tps_id);
        }
        $totalVoters = $voterQuery->count();

        $suppQuery = Voter::where('is_supporter', true);
        if ($user && $user->isSaksi() && $user->tps_id) {
            $suppQuery->where('tps_id', $user->tps_id);
        }
        $totalSupporters = $suppQuery->count();

        $overallPercentage = $totalVoters > 0 ? round(($totalSupporters / $totalVoters) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'summary' => [
                'total_tps'          => $rekap->count(),
                'total_voters'       => $totalVoters,
                'total_supporters'   => $totalSupporters,
                'overall_percentage' => $overallPercentage,
            ],
            'rekap_per_tps' => $rekap,
        ]);
    }
}

