<?php

namespace App\Http\Controllers;

use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Http\Request;

class SupporterController extends Controller
{
    public function index(Request $request)
    {
        $tpsList     = Tps::orderBy('nama_tps')->get();
        $selectedTps = $request->query('tps_id');
        $search      = $request->query('search');

        $supporters = Voter::with('tps')
            ->where('is_supporter', true)
            ->when($selectedTps, fn($q) => $q->where('tps_id', $selectedTps))
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%"))
            ->orderBy('tps_id')
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        $totalSupportersCount = Voter::where('is_supporter', true)->count();

        return view('supporters.index', compact('supporters', 'tpsList', 'selectedTps', 'search', 'totalSupportersCount'));
    }
}
