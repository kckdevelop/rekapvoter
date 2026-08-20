<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tps;
use App\Models\Voter;

class DashboardApiController extends Controller
{
    /**
     * Mobile Dashboard summary stats & chart data.
     */
    public function index()
    {
        $totalTps        = Tps::count();
        $totalVoters     = Voter::count();
        $totalSupporters = Voter::where('is_supporter', true)->count();
        $overallPercentage = $totalVoters > 0 ? round(($totalSupporters / $totalVoters) * 100, 1) : 0;

        $tpsChartData = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ])->orderBy('nama_tps')->get()->map(function ($tps) {
            return [
                'id'               => $tps->id,
                'nama_tps'         => $tps->nama_tps,
                'total_voters'     => $tps->voters_count,
                'total_supporters' => $tps->supporters_count,
                'percentage'       => $tps->voters_count > 0 ? round(($tps->supporters_count / $tps->voters_count) * 100, 1) : 0,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'summary' => [
                    'total_tps'          => $totalTps,
                    'total_voters'       => $totalVoters,
                    'total_supporters'   => $totalSupporters,
                    'overall_percentage' => $overallPercentage,
                ],
                'tps_breakdown' => $tpsChartData,
            ],
        ]);
    }
}
