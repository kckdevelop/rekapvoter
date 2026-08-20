<?php

namespace App\Http\Controllers;

use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data ringkasan
        $totalTps      = Tps::count();
        $totalVoters   = Voter::count();
        $totalSupporters = Voter::where('is_supporter', true)->count();

        // Data untuk Bar Chart per TPS
        $tpsData = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ])->orderBy('nama_tps')->get();

        $chartLabels     = $tpsData->pluck('nama_tps');
        $chartVoters     = $tpsData->pluck('voters_count');
        $chartSupporters = $tpsData->pluck('supporters_count');

        return view('dashboard', compact(
            'totalTps',
            'totalVoters',
            'totalSupporters',
            'chartLabels',
            'chartVoters',
            'chartSupporters'
        ));
    }
}
