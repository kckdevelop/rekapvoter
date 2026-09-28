<?php

namespace App\Http\Controllers;

use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tpsQuery = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ]);

        if ($user->isSaksi() && $user->tps_id) {
            $tpsQuery->where('id', $user->tps_id);
        }

        $tpsData = $tpsQuery->orderBy('nama_tps')->get();

        $voterQuery = Voter::query();
        $suppQuery  = Voter::where('is_supporter', true);

        if ($user->isSaksi() && $user->tps_id) {
            $voterQuery->where('tps_id', $user->tps_id);
            $suppQuery->where('tps_id', $user->tps_id);
        }

        $totalTps        = $tpsData->count();
        $totalVoters     = $voterQuery->count();
        $totalSupporters = $suppQuery->count();

        $chartLabels     = $tpsData->pluck('nama_tps');
        $chartVoters     = $tpsData->pluck('voters_count');
        $chartSupporters = $tpsData->pluck('supporters_count');

        // Quick & Real stats for dashboard overview cards
        $totalQuickSubmitted = $tpsData->where('quick_is_submitted', true)->count();
        $totalRealSubmitted  = $tpsData->where('is_submitted', true)->count();

        return view('dashboard', compact(
            'totalTps',
            'totalVoters',
            'totalSupporters',
            'chartLabels',
            'chartVoters',
            'chartSupporters',
            'totalQuickSubmitted',
            'totalRealSubmitted',
            'user'
        ));
    }
}

