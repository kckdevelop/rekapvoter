<?php

namespace App\Http\Controllers;

use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Http\Request;

class TpsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $tpsList = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ])
        ->when($search, fn($q) => $q->where('nama_tps', 'like', "%{$search}%"))
        ->orderBy('nama_tps')
        ->paginate(15)
        ->withQueryString();

        $totalTpsCount        = Tps::count();
        $totalVotersCount     = Voter::count();
        $totalSupportersCount = Voter::where('is_supporter', true)->count();
        $overallPercentage    = $totalVotersCount > 0 ? round(($totalSupportersCount / $totalVotersCount) * 100, 1) : 0;

        return view('tps.index', compact(
            'tpsList',
            'search',
            'totalTpsCount',
            'totalVotersCount',
            'totalSupportersCount',
            'overallPercentage'
        ));
    }

    public function create()
    {
        return view('tps.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tps' => 'required|string|max:100|unique:tps,nama_tps',
        ], [
            'nama_tps.required' => 'Nama TPS wajib diisi.',
            'nama_tps.unique'   => 'Nama TPS sudah terdaftar.',
        ]);

        Tps::create($request->only('nama_tps'));

        return redirect()->route('tps.index')
            ->with('success', 'TPS berhasil ditambahkan.');
    }

    public function edit(Tps $tp)
    {
        return view('tps.edit', compact('tp'));
    }

    public function update(Request $request, Tps $tp)
    {
        $request->validate([
            'nama_tps' => 'required|string|max:100|unique:tps,nama_tps,' . $tp->id,
        ], [
            'nama_tps.required' => 'Nama TPS wajib diisi.',
            'nama_tps.unique'   => 'Nama TPS sudah terdaftar.',
        ]);

        $tp->update($request->only('nama_tps'));

        return redirect()->route('tps.index')
            ->with('success', 'TPS berhasil diperbarui.');
    }

    public function destroy(Tps $tp)
    {
        $nama = $tp->nama_tps;
        $tp->delete();

        return redirect()->route('tps.index')
            ->with('success', "TPS {$nama} berhasil dihapus beserta data pemilihnya.");
    }
}
