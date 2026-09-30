@extends('layouts.app')

@section('title', 'Input Hasil Real TPS')
@section('page-title', 'Input Hasil Real Pemilihan (Real Count)')
@section('page-subtitle', 'Input & perbarui perolehan suara sah tiap calon lurah & suara tidak sah di masing-masing TPS')

@section('content')
<div class="py-4 space-y-6"
     x-data="{
        editModalOpen: false,
        editId: null,
        editName: '',
        editVotes: {},
        editTidakSah: 0,
        editCatatan: '',

        resetModalOpen: false,
        resetId: null,
        resetName: ''
     }"
     @open-realcount-modal.window="
        editModalOpen = true;
        editId = $event.detail.id;
        editName = $event.detail.name;
        editVotes = $event.detail.votes || {};
        editTidakSah = $event.detail.tidak_sah || 0;
        editCatatan = $event.detail.catatan || '';
     "
     @open-reset-modal.window="
        resetModalOpen = true;
        resetId = $event.detail.id;
        resetName = $event.detail.name;
     ">

    {{-- Alert Notification --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-sm font-bold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-900 text-sm font-bold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 102 0V9a1 1 0 10-2 0v4zm1-8a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">✕</button>
        </div>
    @endif

    {{-- ===== STATISTIC CARDS (DINAMIS PER KANDIDAT) ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-{{ min(6, 2 + $candidates->count()) }} gap-4">
        
        {{-- Card: Progress TPS Input --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-extrabold text-lg shadow-inner">
                📋
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Progress TPS Masuk</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5 tracking-tight">
                    {{ $tpsSubmittedCount }} <span class="text-xs text-slate-400 font-semibold">/ {{ $totalTpsCount }} TPS</span>
                </p>
            </div>
        </div>

        {{-- Cards Perolehan Suara Tiap Kandidat (Utama & Lawan) --}}
        @foreach($candidates as $cand)
        @php
            $totalVotesCand = $candidateTotals[$cand->id] ?? 0;
        @endphp
        @if($cand->is_main_candidate)
            {{-- Card Kandidat Utama --}}
            <div class="bg-gradient-to-br from-rose-600 to-rose-700 text-white rounded-3xl p-5 shadow-lg shadow-rose-950/20 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center font-extrabold text-lg backdrop-blur-md">
                    ⭐
                </div>
                <div>
                    <p class="text-rose-200 text-[11px] font-extrabold uppercase tracking-wider">Suara {{ Str::words($cand->nama, 2) }}</p>
                    <p class="text-2xl font-extrabold tracking-tight mt-0.5">{{ number_format($totalVotesCand) }}</p>
                </div>
            </div>
        @else
            {{-- Card Calon Lawan Individual --}}
            <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl text-white flex items-center justify-center font-black text-sm shadow-sm" style="background-color: {{ $cand->warna_badge }}">
                    {{ sprintf('%02d', $cand->nomor_urut) }}
                </div>
                <div>
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Suara {{ Str::words($cand->nama, 2) }}</p>
                    <p class="text-2xl font-extrabold text-slate-800 mt-0.5 tracking-tight">{{ number_format($totalVotesCand) }}</p>
                </div>
            </div>
        @endif
        @endforeach

        {{-- Card: Suara Tidak Sah --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-extrabold text-lg shadow-inner">
                ⚠️
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Suara Tidak Sah / Rusak</p>
                <p class="text-2xl font-extrabold text-amber-700 mt-0.5 tracking-tight">{{ number_format($totalSuaraTidakSah) }}</p>
            </div>
        </div>

    </div>

    {{-- ===== SEARCH & ACTION BAR ===== --}}
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('realcount.index') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto flex-1">
            <div class="relative flex-1 min-w-56">
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nama TPS..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition-colors">
                Cari
            </button>
            @if($search)
                <a href="{{ route('realcount.index') }}" class="text-xs text-rose-600 hover:text-rose-800 bg-rose-50 px-3 py-2.5 rounded-xl font-bold transition-colors">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('candidates.index') }}" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2.5 rounded-xl transition-colors text-xs border border-slate-200">
            <span>⚙️ Pengaturan Data Lawan ({{ $candidates->where('is_main_candidate', false)->count() }} Lawan)</span>
        </a>
    </div>

    {{-- ===== TABLE LIST TPS INPUT ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse min-w-[950px]">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200 text-slate-500 text-[11px] font-extrabold uppercase tracking-wider">
                        <th class="px-6 py-4 w-12 text-center">#</th>
                        <th class="px-6 py-4">Nama TPS</th>
                        <th class="px-6 py-4 text-center">DPT / Pendukung</th>

                        {{-- Dynamic Columns Header for Each Candidate --}}
                        @foreach($candidates as $cand)
                        <th class="px-4 py-4 text-center">
                            <span class="inline-block px-2.5 py-1 rounded-lg text-white font-black text-[10px]" style="background-color: {{ $cand->warna_badge }}">
                                No. {{ sprintf('%02d', $cand->nomor_urut) }}
                            </span>
                            <span class="block text-xs font-extrabold text-slate-800 mt-1">{{ Str::words($cand->nama, 2) }}</span>
                        </th>
                        @endforeach

                        <th class="px-4 py-4 text-center">Tidak Sah</th>
                        <th class="px-4 py-4 text-center">Status Input</th>
                        <th class="px-4 py-4 text-center">Status Kemenangan</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tpsList as $tps)
                    @php
                        // Map existing votes per candidate ID for JS dispatch & calculate per-TPS victory status
                        $votesMap = [];
                        $mainVote = 0;
                        $maxOpponentVote = 0;
                        $totalOpponentVote = 0;

                        foreach($candidates as $cand) {
                            $res = $tps->candidateResults->firstWhere('candidate_id', $cand->id);
                            $val = $res ? $res->jumlah_suara : 0;
                            $votesMap[$cand->id] = $val;

                            if ($cand->is_main_candidate) {
                                $mainVote = $val;
                            } else {
                                $totalOpponentVote += $val;
                                if ($val >= $maxOpponentVote) {
                                    $maxOpponentVote = $val;
                                }
                            }
                        }

                        $tpsSuaraSah = $mainVote + $totalOpponentVote;
                        $tpsPctMain = $tpsSuaraSah > 0 ? round(($mainVote / $tpsSuaraSah) * 100, 1) : 0;
                        $tpsMargin  = $mainVote - $maxOpponentVote;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-center text-slate-400 font-semibold text-xs">
                            {{ $loop->iteration + ($tpsList->currentPage()-1)*$tpsList->perPage() }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-extrabold text-slate-900 text-sm block">{{ $tps->nama_tps }}</span>
                            @if($tps->catatan_saksi)
                                <span class="text-xs text-slate-400 italic block mt-0.5">💬 {{ Str::limit($tps->catatan_saksi, 25) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="text-xs font-bold text-slate-700">{{ number_format($tps->voters_count) }} DPT</span>
                                <span class="text-[11px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200/60 mt-0.5">
                                    ⭐ {{ number_format($tps->supporters_count) }} Pendukung
                                </span>
                            </div>
                        </td>

                        {{-- Perolehan Suara Tiap Kandidat --}}
                        @foreach($candidates as $cand)
                        @php
                            $voteVal = $votesMap[$cand->id] ?? 0;
                        @endphp
                        <td class="px-4 py-4 text-center">
                            @if($tps->is_submitted)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl font-extrabold text-xs {{ $cand->is_main_candidate ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-slate-100 text-slate-800 border border-slate-200' }}">
                                    {{ number_format($voteVal) }}
                                </span>
                            @else
                                <span class="text-slate-300 font-bold">—</span>
                            @endif
                        </td>
                        @endforeach

                        <td class="px-4 py-4 text-center">
                            @if($tps->is_submitted)
                                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/60">
                                    {{ number_format($tps->suara_tidak_sah) }}
                                </span>
                            @else
                                <span class="text-slate-300 font-bold">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($tps->is_submitted)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    ✓ Terinput
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    ⏳ Belum Input
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if(!$tps->is_submitted)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                    — Belum Input
                                </span>
                            @elseif($mainVote > $maxOpponentVote)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm" title="Unggul {{ $tpsMargin > 0 ? '+'.$tpsMargin : $tpsMargin }} suara dibanding lawan terkuat">
                                    🏆 Menang ({{ $tpsPctMain }}%)
                                </span>
                            @elseif($mainVote == $maxOpponentVote)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300 shadow-sm" title="Suara imbang dengan lawan">
                                    ⚖️ Seri ({{ $tpsPctMain }}%)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-300 shadow-sm" title="Tertinggal {{ abs($tpsMargin) }} suara dari lawan terkuat">
                                    📌 Kalah ({{ $tpsPctMain }}%)
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Tombol Input / Edit Hasil --}}
                                <button type="button"
                                        @click="$dispatch('open-realcount-modal', {
                                            id: {{ $tps->id }},
                                            name: '{{ addslashes($tps->nama_tps) }}',
                                            votes: {{ json_encode($votesMap) }},
                                            tidak_sah: {{ $tps->suara_tidak_sah ?? 0 }},
                                            catatan: '{{ addslashes($tps->catatan_saksi ?? '') }}'
                                        })"
                                        class="inline-flex items-center gap-1 text-xs bg-rose-600 hover:bg-rose-700 text-white font-extrabold px-3 py-2 rounded-xl transition-colors shadow-sm cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>{{ $tps->is_submitted ? 'Edit Hasil' : 'Input Hasil' }}</span>
                                </button>

                                {{-- Tombol Reset (hanya muncul jika sudah ada data) --}}
                                @if($tps->is_submitted)
                                <button type="button"
                                        @click="$dispatch('open-reset-modal', {
                                            id: {{ $tps->id }},
                                            name: '{{ addslashes($tps->nama_tps) }}'
                                        })"
                                        title="Reset data Real Count {{ $tps->nama_tps }}"
                                        class="inline-flex items-center gap-1 text-xs bg-rose-100 hover:bg-rose-600 text-rose-700 hover:text-white font-extrabold px-3 py-2 rounded-xl transition-all border border-rose-200 hover:border-rose-600 shadow-sm cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span>Reset</span>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 7 + $candidates->count() }}" class="px-6 py-12 text-center text-slate-400 font-semibold">Belum Ada Data TPS</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tpsList->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $tpsList->links() }}
        </div>
        @endif
    </div>

    {{-- ===== MODAL INPUT / EDIT HASIL REAL COUNT TPS (DINAMIS SEMUA CANDIDATES) ===== --}}
    <div x-show="editModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md"
         style="display: none;">

        <div @click.away="editModalOpen = false"
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-lg p-6 space-y-5 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-lg">
                        🗳️
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base" x-text="'Input Hasil Real — ' + editName"></h3>
                        <p class="text-slate-400 text-xs">Masukkan perolehan suara tiap calon &amp; suara tidak sah</p>
                    </div>
                </div>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'/realcount/' + editId" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Dynamic Input For Every Registered Candidate --}}
                @foreach($candidates as $cand)
                <div class="p-4 rounded-2xl border space-y-2 {{ $cand->is_main_candidate ? 'bg-emerald-50/80 border-emerald-200/80' : 'bg-slate-50 border-slate-200' }}">
                    <div class="flex items-center justify-between">
                        <label for="vote_cand_{{ $cand->id }}" class="block text-xs font-extrabold uppercase tracking-wider {{ $cand->is_main_candidate ? 'text-emerald-900' : 'text-slate-700' }}">
                            @if($cand->is_main_candidate)
                                ⭐ No. {{ sprintf('%02d', $cand->nomor_urut) }} — {{ $cand->nama }} (Kandidat Utama) <span class="text-emerald-600">*</span>
                            @else
                                👥 No. {{ sprintf('%02d', $cand->nomor_urut) }} — {{ $cand->nama }} (Calon Lawan) <span class="text-rose-500">*</span>
                            @endif
                        </label>
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $cand->warna_badge }}"></span>
                    </div>

                    <input type="number" id="vote_cand_{{ $cand->id }}" name="votes[{{ $cand->id }}]"
                           x-model.number="editVotes[{{ $cand->id }}]" required min="0" placeholder="0"
                           class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-base font-extrabold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm">
                </div>
                @endforeach

                {{-- Suara Tidak Sah --}}
                <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-4 space-y-2">
                    <label for="suara_tidak_sah" class="block text-xs font-extrabold text-amber-900 uppercase tracking-wider">
                        ⚠️ Suara Tidak Sah / Rusak
                    </label>
                    <input type="number" id="suara_tidak_sah" name="suara_tidak_sah" x-model.number="editTidakSah" min="0" placeholder="0"
                           class="w-full bg-white border border-amber-300 rounded-xl px-4 py-3 text-base font-extrabold text-amber-950 focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-sm">
                </div>

                {{-- Catatan Saksi --}}
                <div>
                    <label for="catatan_saksi" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                        💬 Catatan / Keterangan Saksi (Opsional)
                    </label>
                    <textarea id="catatan_saksi" name="catatan_saksi" x-model="editCatatan" rows="2"
                              placeholder="Masukkan catatan khusus dari saksi TPS jika ada..."
                              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:bg-white transition-all"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-sm hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-sm transition-colors shadow-md shadow-rose-900/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Simpan Hasil Real
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL KONFIRMASI RESET REAL COUNT ===== --}}
    <div x-show="resetModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md"
         style="display: none;">

        <div @click.away="resetModalOpen = false"
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md p-7 space-y-5">

            {{-- Header --}}
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 rounded-2xl bg-rose-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-extrabold text-slate-900 text-base">Konfirmasi Reset Real Count</h3>
                    <p class="text-slate-500 text-xs mt-1">Tindakan ini akan <span class="font-extrabold text-rose-600">menghapus seluruh data</span> hasil real pemilihan pada:</p>
                    <p class="mt-2 text-sm font-extrabold text-rose-700 bg-rose-50 border border-rose-200 px-3 py-2 rounded-xl" x-text="resetName"></p>
                    <p class="text-[11px] text-slate-400 mt-2">⚠️ Data perolehan suara sah tiap calon, suara tidak sah, serta catatan saksi akan direset ke 0 dan status TPS akan kembali ke <strong>Belum Input</strong>. Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>

            {{-- Form Reset --}}
            <form :action="'/realcount/' + resetId + '/reset'" method="POST" class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                @csrf
                @method('DELETE')
                <button type="button" @click="resetModalOpen = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-sm hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-sm transition-colors shadow-md shadow-rose-900/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Ya, Reset Real Count
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
