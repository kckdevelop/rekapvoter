<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    /**
     * Tampilkan Pengaturan Data Calon Lurah (Main & Lawan)
     */
    public function index()
    {
        // Auto-seed kandidat utama jika belum ada
        if (Candidate::count() === 0) {
            Candidate::create([
                'nomor_urut' => 1,
                'nama' => 'Nurma Setiawan, SE',
                'is_main_candidate' => true,
                'warna_badge' => '#059669',
            ]);

            Candidate::create([
                'nomor_urut' => 2,
                'nama' => 'Drs. H. Subagyo, M.Si',
                'is_main_candidate' => false,
                'warna_badge' => '#2563eb',
            ]);
        }

        $candidates = Candidate::orderBy('nomor_urut')->get();

        return view('candidates.index', compact('candidates'));
    }

    /**
     * Tambah Calon Lawan Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_urut'  => 'required|integer|min:1|unique:candidates,nomor_urut',
            'nama'        => 'required|string|max:255',
            'warna_badge' => 'nullable|string|max:20',
        ], [
            'nomor_urut.required' => 'Nomor urut wajib diisi.',
            'nomor_urut.unique'   => 'Nomor urut sudah digunakan oleh calon lain.',
            'nama.required'       => 'Nama calon wajib diisi.',
        ]);

        Candidate::create([
            'nomor_urut'        => $validated['nomor_urut'],
            'nama'              => $validated['nama'],
            'is_main_candidate' => false,
            'warna_badge'       => $validated['warna_badge'] ?? '#3b82f6',
        ]);

        return redirect()->route('candidates.index')->with('success', "Calon lawan '{$validated['nama']}' berhasil ditambahkan.");
    }

    /**
     * Perbarui Data Calon
     */
    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'nomor_urut'  => 'required|integer|min:1|unique:candidates,nomor_urut,' . $candidate->id,
            'nama'        => 'required|string|max:255',
            'warna_badge' => 'nullable|string|max:20',
        ], [
            'nomor_urut.required' => 'Nomor urut wajib diisi.',
            'nomor_urut.unique'   => 'Nomor urut sudah digunakan oleh calon lain.',
            'nama.required'       => 'Nama calon wajib diisi.',
        ]);

        $candidate->update([
            'nomor_urut'  => $validated['nomor_urut'],
            'nama'        => $validated['nama'],
            'warna_badge' => $validated['warna_badge'] ?? $candidate->warna_badge,
        ]);

        return redirect()->route('candidates.index')->with('success', "Data calon '{$candidate->nama}' berhasil diperbarui.");
    }

    /**
     * Hapus Calon Lawan
     */
    public function destroy(Candidate $candidate)
    {
        if ($candidate->is_main_candidate) {
            return redirect()->back()->with('error', 'Kandidat utama Nurma Setiawan, SE tidak dapat dihapus.');
        }

        $nama = $candidate->nama;
        $candidate->delete();

        return redirect()->route('candidates.index')->with('success', "Calon lawan '{$nama}' berhasil dihapus.");
    }
}
