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
    public function index()
    {
        $tpsList = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ])->orderBy('nama_tps')->get()->map(function ($tps) {
            $pct = $tps->voters_count > 0 ? round(($tps->supporters_count / $tps->voters_count) * 100, 1) : 0;
            return [
                'id'               => $tps->id,
                'nama_tps'         => $tps->nama_tps,
                'total_voters'     => $tps->voters_count,
                'total_supporters' => $tps->supporters_count,
                'percentage'       => $pct,
                'created_at'       => $tps->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $tpsList,
        ]);
    }

    /**
     * Store new TPS via mobile API.
     */
    public function store(Request $request)
    {
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
