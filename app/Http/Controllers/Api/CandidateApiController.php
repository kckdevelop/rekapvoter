<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\Request;

class CandidateApiController extends Controller
{
    /**
     * List all candidates (main & opponents).
     */
    public function index()
    {
        $candidates = Candidate::orderBy('nomor_urut')->get()->map(function ($c) {
            return [
                'id'                => $c->id,
                'nomor_urut'        => $c->nomor_urut,
                'nama'              => $c->nama,
                'is_main_candidate' => $c->is_main_candidate,
                'warna_badge'       => $c->warna_badge,
                'created_at'        => $c->created_at?->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $candidates,
        ]);
    }

    /**
     * Get single candidate by ID.
     */
    public function show(Candidate $candidate)
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'id'                => $candidate->id,
                'nomor_urut'        => $candidate->nomor_urut,
                'nama'              => $candidate->nama,
                'is_main_candidate' => $candidate->is_main_candidate,
                'warna_badge'       => $candidate->warna_badge,
                'created_at'        => $candidate->created_at?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Store a new candidate.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_urut'        => 'required|integer|min:1|unique:candidates,nomor_urut',
            'nama'              => 'required|string|max:255',
            'is_main_candidate' => 'required|boolean',
            'warna_badge'       => 'nullable|string|max:20',
        ], [
            'nomor_urut.required'        => 'Nomor urut wajib diisi.',
            'nomor_urut.unique'          => 'Nomor urut sudah digunakan.',
            'nama.required'              => 'Nama kandidat wajib diisi.',
            'is_main_candidate.required' => 'Status kandidat utama wajib ditentukan.',
        ]);

        $candidate = Candidate::create([
            'nomor_urut'        => $request->nomor_urut,
            'nama'              => $request->nama,
            'is_main_candidate' => (bool) $request->is_main_candidate,
            'warna_badge'       => $request->warna_badge ?? '#059669',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kandidat berhasil ditambahkan.',
            'data'    => $candidate,
        ], 201);
    }

    /**
     * Update candidate data.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $request->validate([
            'nomor_urut'        => "sometimes|integer|min:1|unique:candidates,nomor_urut,{$candidate->id}",
            'nama'              => 'sometimes|string|max:255',
            'is_main_candidate' => 'sometimes|boolean',
            'warna_badge'       => 'nullable|string|max:20',
        ]);

        $candidate->update($request->only(['nomor_urut', 'nama', 'is_main_candidate', 'warna_badge']));

        return response()->json([
            'success' => true,
            'message' => 'Kandidat berhasil diperbarui.',
            'data'    => $candidate->fresh(),
        ]);
    }

    /**
     * Delete candidate.
     */
    public function destroy(Candidate $candidate)
    {
        $candidate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kandidat berhasil dihapus.',
        ]);
    }
}
