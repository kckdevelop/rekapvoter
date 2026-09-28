<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tps;
use Illuminate\Http\Request;

class TpsApiController extends Controller
{
    /**
     * List all TPS with voter & supporter counts.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ]);

        if ($user && $user->isSaksi() && $user->tps_id) {
            $query->where('id', $user->tps_id);
        }

        $tpsList = $query->orderBy('nama_tps')->get()->map(function ($tps) {
            $pct = $tps->voters_count > 0 ? round(($tps->supporters_count / $tps->voters_count) * 100, 1) : 0;
            return [
                'id'                 => $tps->id,
                'nama_tps'           => $tps->nama_tps,
                'total_voters'       => $tps->voters_count,
                'total_supporters'   => $tps->supporters_count,
                'percentage'         => $pct,
                'quick_is_submitted' => $tps->quick_is_submitted,
                'real_is_submitted'  => $tps->is_submitted,
                'created_at'         => $tps->created_at?->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $tpsList,
        ]);
    }

    /**
     * Store new TPS via mobile API (Admin only).
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Administrator yang dapat menambahkan TPS baru.',
            ], 403);
        }

        $request->validate([
            'nama_tps' => 'required|string|max:100|unique:tps,nama_tps',
        ], [
            'nama_tps.required' => 'Nama TPS wajib diisi.',
            'nama_tps.unique'   => 'Nama TPS sudah terdaftar.',
        ]);

        $tps = Tps::create($request->only('nama_tps'));

        return response()->json([
            'success' => true,
            'message' => 'TPS berhasil ditambahkan.',
            'data'    => $tps,
        ], 201);
    }
}

