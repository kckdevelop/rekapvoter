<?php

namespace App\Http\Controllers;

use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Http\Request;

class SupporterController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedTps = $request->query('tps_id');
        $search      = $request->query('search');

        if ($user->isSaksi() && $user->tps_id) {
            $tpsList     = Tps::where('id', $user->tps_id)->get();
            $selectedTps = $user->tps_id;
        } else {
            $tpsList     = Tps::orderBy('nama_tps')->get();
        }

        $supporters = Voter::with('tps')
            ->where('is_supporter', true)
            ->when($selectedTps, fn($q) => $q->where('tps_id', $selectedTps))
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%"))
            ->orderBy('tps_id')
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        $totalSupportersQuery = Voter::where('is_supporter', true);
        if ($user->isSaksi() && $user->tps_id) {
            $totalSupportersQuery->where('tps_id', $user->tps_id);
        }
        $totalSupportersCount = $totalSupportersQuery->count();

        return view('supporters.index', compact('supporters', 'tpsList', 'selectedTps', 'search', 'totalSupportersCount', 'user'));
    }
}
