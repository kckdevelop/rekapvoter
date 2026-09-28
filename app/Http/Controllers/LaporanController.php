<?php

namespace App\Http\Controllers;

use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
            'voters as non_supporters_count' => fn($q) => $q->where('is_supporter', false),
        ]);

        if ($user->isSaksi() && $user->tps_id) {
            $query->where('id', $user->tps_id);
        }

        $rekapTps = $query->orderBy('nama_tps')->get();

        $totalTps       = $rekapTps->count();
        $totalVoters    = $rekapTps->sum('voters_count');
        $totalSupporters= $rekapTps->sum('supporters_count');
        $totalNonSupporters = $totalVoters - $totalSupporters;
        $overallPercentage  = $totalVoters > 0 ? round(($totalSupporters / $totalVoters) * 100, 1) : 0;

        return view('laporan.index', compact(
            'rekapTps',
            'totalTps',
            'totalVoters',
            'totalSupporters',
            'totalNonSupporters',
            'overallPercentage',
            'user'
        ));
    }

    public function print(Request $request)
    {
        $user = $request->user();
        $query = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
            'voters as non_supporters_count' => fn($q) => $q->where('is_supporter', false),
        ]);

        if ($user->isSaksi() && $user->tps_id) {
            $query->where('id', $user->tps_id);
        }

        $rekapTps = $query->orderBy('nama_tps')->get();

        $totalVoters    = $rekapTps->sum('voters_count');
        $totalSupporters= $rekapTps->sum('supporters_count');
        $overallPercentage = $totalVoters > 0 ? round(($totalSupporters / $totalVoters) * 100, 1) : 0;

        return view('laporan.print', compact('rekapTps', 'totalVoters', 'totalSupporters', 'overallPercentage', 'user'));
    }
}
