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
        $user = $request->user();
        $selectedTps      = $request->query('tps_id');
        $search           = $request->query('search');
        $statusSupporter  = $request->query('status_supporter');

        if ($user->isSaksi() && $user->tps_id) {
            $tpsList     = Tps::where('id', $user->tps_id)->get();
            $selectedTps = $user->tps_id;
        } else {
            $tpsList     = Tps::orderBy('nama_tps')->get();
        }

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

        return view('voters.index', compact('voters', 'tpsList', 'selectedTps', 'search', 'statusSupporter', 'user'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if ($user->isSaksi() && $user->tps_id) {
            $tpsList = Tps::where('id', $user->tps_id)->get();
        } else {
            $tpsList = Tps::orderBy('nama_tps')->get();
        }
        return view('voters.create', compact('tpsList', 'user'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $targetTpsId = ($user->isSaksi() && $user->tps_id) ? $user->tps_id : $request->tps_id;

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

        Voter::create([
            'tps_id'       => $targetTpsId,
            'nama'         => $request->nama,
            'is_supporter' => $request->has('is_supporter') ? (bool) $request->is_supporter : false,
        ]);

        return redirect()->route('voters.index')
            ->with('success', 'Pemilih berhasil ditambahkan.');
    }

    public function edit(Request $request, Voter $voter)
    {
        $user = $request->user();
        if ($user->isSaksi() && $user->tps_id !== $voter->tps_id) {
            return redirect()->route('voters.index')->with('error', 'Akses ditolak. Pemilih ini bukan berada di TPS Anda.');
        }

        if ($user->isSaksi() && $user->tps_id) {
            $tpsList = Tps::where('id', $user->tps_id)->get();
        } else {
            $tpsList = Tps::orderBy('nama_tps')->get();
        }
        return view('voters.edit', compact('voter', 'tpsList', 'user'));
    }

    public function update(Request $request, Voter $voter)
    {
        $user = $request->user();
        if ($user->isSaksi() && $user->tps_id !== $voter->tps_id) {
            return redirect()->route('voters.index')->with('error', 'Akses ditolak. Pemilih ini bukan berada di TPS Anda.');
        }

        $targetTpsId = ($user->isSaksi() && $user->tps_id) ? $user->tps_id : $request->tps_id;
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

        $voter->update([
            'tps_id'       => $targetTpsId,
            'nama'         => $request->nama,
            'is_supporter' => $request->has('is_supporter') ? (bool) $request->is_supporter : false,
        ]);

        return redirect()->route('voters.index')
            ->with('success', 'Data pemilih berhasil diperbarui.');
    }

    public function destroy(Request $request, Voter $voter)
    {
        $user = $request->user();
        if ($user->isSaksi() && $user->tps_id !== $voter->tps_id) {
            return redirect()->route('voters.index')->with('error', 'Akses ditolak. Pemilih ini bukan berada di TPS Anda.');
        }

        $voter->delete();
        return redirect()->route('voters.index')
            ->with('success', 'Data pemilih berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $user = $request->user();
        $targetTpsId = ($user->isSaksi() && $user->tps_id) ? $user->tps_id : $request->tps_id;
        $request->merge(['tps_id' => $targetTpsId]);

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

        $import = new VotersImport((int) $targetTpsId);
        Excel::import($import, $request->file('file'));

        $tps   = Tps::find($targetTpsId);
        $count = $import->getImportedCount();

        return redirect()->route('voters.index', ['tps_id' => $targetTpsId])
            ->with('success', "Berhasil mengimport {$count} data pemilih ke {$tps->nama_tps}.");
    }

    /**
     * Download template Excel import pemilih (hanya kolom "nama").
     */
    public function downloadTemplate()
    {
        return Excel::download(new VotersTemplateExport(), 'template_import_pemilih.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function toggleSupporter(Request $request, Voter $voter)
    {
        $user = $request->user();
        if ($user->isSaksi() && $user->tps_id !== $voter->tps_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

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
        $user = $request->user();
        $request->validate([
            'voter_ids'    => 'required|array',
            'voter_ids.*'  => 'exists:voters,id',
            'is_supporter' => 'required|boolean',
        ]);

        $query = Voter::whereIn('id', $request->voter_ids);
        if ($user->isSaksi() && $user->tps_id) {
            $query->where('tps_id', $user->tps_id);
        }

        $isSupporter = (bool) $request->is_supporter;
        $count = $query->update(['is_supporter' => $isSupporter]);
        $statusText = $isSupporter ? 'Pendukung' : 'Pemilih Biasa';

        return response()->json([
            'success'      => true,
            'is_supporter' => $isSupporter,
            'voter_ids'    => $request->voter_ids,
            'message'      => "Berhasil memperbarui {$count} pemilih menjadi {$statusText}.",
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'voter_ids'   => 'required|array',
            'voter_ids.*' => 'exists:voters,id',
        ]);

        $query = Voter::whereIn('id', $request->voter_ids);
        if ($user->isSaksi() && $user->tps_id) {
            $query->where('tps_id', $user->tps_id);
        }

        $count = $query->delete();

        return response()->json([
            'success'   => true,
            'voter_ids' => $request->voter_ids,
            'message'   => "Berhasil menghapus {$count} data pemilih.",
        ]);
    }
}
