<?php

namespace App\Http\Controllers;

use App\Exports\VotersTemplateExport;
use App\Imports\VotersImport;
use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VoterController extends Controller
{
    public function index(Request $request)
    {
        $tpsList          = Tps::orderBy('nama_tps')->get();
        $selectedTps      = $request->query('tps_id');
        $search           = $request->query('search');
        $statusSupporter = $request->query('status_supporter');

        $voters = Voter::with('tps')
            ->when($selectedTps, fn($q) => $q->where('tps_id', $selectedTps))
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%"))
            ->when($statusSupporter !== null && $statusSupporter !== '', function ($q) use ($statusSupporter) {
                return $q->where('is_supporter', (bool) $statusSupporter);
            })
            ->orderBy('tps_id')
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        return view('voters.index', compact('voters', 'tpsList', 'selectedTps', 'search', 'statusSupporter'));
    }

    public function create()
    {
        $tpsList = Tps::orderBy('nama_tps')->get();
        return view('voters.create', compact('tpsList'));
    }

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

        Voter::create([
            'tps_id'       => $request->tps_id,
            'nama'         => $request->nama,
            'is_supporter' => $request->has('is_supporter') ? (bool) $request->is_supporter : false,
        ]);

        return redirect()->route('voters.index')
            ->with('success', 'Pemilih berhasil ditambahkan.');
    }

    public function edit(Voter $voter)
    {
        $tpsList = Tps::orderBy('nama_tps')->get();
        return view('voters.edit', compact('voter', 'tpsList'));
    }

    public function update(Request $request, Voter $voter)
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

        $voter->update([
            'tps_id'       => $request->tps_id,
            'nama'         => $request->nama,
            'is_supporter' => $request->has('is_supporter') ? (bool) $request->is_supporter : false,
        ]);

        return redirect()->route('voters.index')
            ->with('success', 'Data pemilih berhasil diperbarui.');
    }

    public function destroy(Voter $voter)
    {
        $voter->delete();
        return redirect()->route('voters.index')
            ->with('success', 'Data pemilih berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'tps_id' => 'required|exists:tps,id',
            'file'   => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'tps_id.required' => 'Pilih TPS terlebih dahulu.',
            'tps_id.exists'   => 'TPS yang dipilih tidak valid.',
            'file.required'   => 'Pilih file Excel terlebih dahulu.',
            'file.mimes'      => 'Format file harus xlsx, xls, atau csv.',
            'file.max'        => 'Ukuran file maksimal 5MB.',
        ]);

        $import = new VotersImport((int) $request->tps_id);
        Excel::import($import, $request->file('file'));

        $tps   = Tps::find($request->tps_id);
        $count = $import->getImportedCount();

        return redirect()->route('voters.index', ['tps_id' => $request->tps_id])
            ->with('success', "Berhasil mengimport {$count} data pemilih ke {$tps->nama_tps}.");
    }

    /**
     * Download template Excel import pemilih (hanya kolom "nama").
     */
    public function downloadTemplate()
    {
        return Excel::download(new VotersTemplateExport(), 'template_import_pemilih.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function toggleSupporter(Voter $voter)
    {
        $voter->update(['is_supporter' => ! $voter->is_supporter]);

        return response()->json([
            'success'      => true,
            'is_supporter' => $voter->is_supporter,
            'message'      => $voter->is_supporter
                ? "{$voter->nama} ditandai sebagai Pendukung."
                : "{$voter->nama} bukan lagi Pendukung.",
        ]);
    }

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
            'voter_ids'    => $voterIds,
            'message'      => "Berhasil memperbarui {$count} pemilih menjadi {$statusText}.",
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'voter_ids'   => 'required|array',
            'voter_ids.*' => 'exists:voters,id',
        ]);

        $voterIds = $request->voter_ids;
        $count    = count($voterIds);

        Voter::whereIn('id', $voterIds)->delete();

        return response()->json([
            'success'   => true,
            'voter_ids' => $voterIds,
            'message'   => "Berhasil menghapus {$count} data pemilih.",
        ]);
    }
}
