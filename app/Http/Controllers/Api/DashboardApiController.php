<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    /**
     * Mobile Dashboard summary stats & chart data.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $tpsQuery = Tps::withCount([
            'voters',
            'voters as supporters_count' => fn($q) => $q->where('is_supporter', true),
        ]);

        if ($user && $user->isSaksi() && $user->tps_id) {
            $tpsQuery->where('id', $user->tps_id);
        }

        $allTps = $tpsQuery->orderBy('nama_tps')->get();

        $voterQuery = Voter::query();
        $suppQuery  = Voter::where('is_supporter', true);

        if ($user && $user->isSaksi() && $user->tps_id) {
            $voterQuery->where('tps_id', $user->tps_id);
            $suppQuery->where('tps_id', $user->tps_id);
        }

        $totalTps        = $allTps->count();
        $totalVoters     = $voterQuery->count();
        $totalSupporters = $suppQuery->count();
        $overallPercentage = $totalVoters > 0 ? round(($totalSupporters / $totalVoters) * 100, 1) : 0;

        $tpsChartData = $allTps->map(function ($tps) {
            return [
                'id'                      => $tps->id,
                'nama_tps'                => $tps->nama_tps,
                'total_voters'            => $tps->voters_count,
                'total_supporters'        => $tps->supporters_count,
                'percentage'              => $tps->voters_count > 0 ? round(($tps->supporters_count / $tps->voters_count) * 100, 1) : 0,
                // Quick Count status
                'quick_is_submitted'      => $tps->quick_is_submitted,
                'quick_suara_kandidat'    => $tps->quick_suara_kandidat ?? 0,
                'quick_suara_lawan'       => $tps->quick_suara_lawan ?? 0,
                'quick_suara_sah'         => $tps->quick_suara_sah ?? 0,
                // Real Count status
                'real_is_submitted'       => $tps->is_submitted,
                'real_suara_kandidat'     => $tps->suara_kandidat ?? 0,
                'real_suara_lawan'        => $tps->suara_lawan ?? 0,
                'real_suara_sah'          => $tps->suara_sah ?? 0,
            ];
        });

        // Quick Count vs Real Count summary
        $quickSubmitted = $allTps->where('quick_is_submitted', true)->count();
        $realSubmitted  = $allTps->where('is_submitted', true)->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'user'    => [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'role'   => $user->role ?? 'admin',
                    'tps_id' => $user->tps_id,
                    'tps'    => $user->tps ? ['id' => $user->tps->id, 'nama_tps' => $user->tps->nama_tps] : null,
                ],
                'summary' => [
                    'total_tps'          => $totalTps,
                    'total_voters'       => $totalVoters,
                    'total_supporters'   => $totalSupporters,
                    'overall_percentage' => $overallPercentage,
                    'quick_submitted'    => $quickSubmitted,
                    'real_submitted'     => $realSubmitted,
                ],
                'tps_breakdown' => $tpsChartData,
            ],
        ]);
    }
}

