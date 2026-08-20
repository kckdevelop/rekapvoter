@extends('layouts.app')

@section('title', 'Daftar Pendukung')
@section('page-title', 'Daftar Khusus Pendukung')
@section('page-subtitle', 'Menampilkan hanya pemilih yang sudah terverifikasi sebagai pendukung calon')

@section('content')
<div class="py-4 space-y-6">

    {{-- ===== SUMMARY BANNER ===== --}}
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-3xl p-6 sm:p-8 shadow-lg shadow-emerald-950/20 relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-white/20 text-white backdrop-blur-sm mb-3">
                    ⭐ Total Terdata
                </span>
                <h2 class="text-3xl font-extrabold tracking-tight">{{ number_format($totalSupportersCount) }} Pendukung</h2>
                <p class="text-emerald-100 text-xs mt-1">Seluruh pemilih dengan status is_supporter = true di semua TPS</p>
            </div>
            <a href="{{ route('voters.index') }}" class="inline-flex items-center gap-2 bg-white text-emerald-800 font-extrabold px-5 py-3 rounded-2xl hover:bg-emerald-50 transition-all text-xs shadow-md">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Kelola / Tambah Pemilih</span>
            </a>
        </div>
        <div class="absolute -right-8 -bottom-8 opacity-10 pointer-events-none">
            <svg class="w-48 h-48 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
    </div>

    {{-- ===== FILTER & SEARCH BAR ===== --}}
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm">
        <form action="{{ route('supporters.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                
                {{-- Search Box --}}
                <div class="relative flex-1 min-w-56">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama pendukung..."
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                {{-- Filter TPS --}}
                <div class="relative min-w-48">
                    <select name="tps_id" onchange="this.form.submit()"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all appearance-none pr-10">
                        <option value="">-- Semua TPS --</option>
                        @foreach($tpsList as $tps)
                            <option value="{{ $tps->id }}" {{ $selectedTps == $tps->id ? 'selected' : '' }}>
                                {{ $tps->nama_tps }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute right-3.5 top-3.5 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition-colors">
                    Cari
                </button>

                @if($selectedTps || $search)
                    <a href="{{ route('supporters.index') }}" class="inline-flex items-center gap-1 text-xs text-rose-600 hover:text-rose-800 bg-rose-50 px-3 py-2 rounded-xl font-bold transition-colors">
                        <span>✕ Reset</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ===== SUPPORTERS TABLE ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse min-w-[650px]">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200 text-slate-500 text-[11px] font-extrabold uppercase tracking-wider">
                        <th class="px-6 py-4 w-12 text-center">#</th>
                        <th class="px-6 py-4">Nama Pendukung</th>
                        <th class="px-6 py-4">Tempat Pemungutan Suara (TPS)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($supporters as $voter)
                    <tr id="supporter-row-{{ $voter->id }}" class="bg-emerald-50/70 hover:bg-emerald-100/60 transition-colors border-l-4 border-l-emerald-500">
                        <td class="px-6 py-4 text-center text-slate-400 font-semibold text-xs">
                            {{ $loop->iteration + ($supporters->currentPage()-1)*$supporters->perPage() }}
                        </td>
                        <td class="px-6 py-4 font-bold text-emerald-950 text-sm">
                            {{ $voter->nama }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200/80">
                                📍 {{ $voter->tps->nama_tps }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-600 text-white shadow-sm shadow-emerald-900/20">
                                ✓ Pendukung Calon
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button"
                                    onclick="removeSupporter({{ $voter->id }})"
                                    id="btn-remove-{{ $voter->id }}"
                                    class="inline-flex items-center gap-1 text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold px-3 py-1.5 rounded-xl border border-rose-200/60 transition-colors">
                                Batalkan Pendukung
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                </div>
                                <p class="text-slate-700 font-extrabold text-base">Tidak Ada Pendukung Ditemukan</p>
                                <p class="text-slate-400 text-xs mt-1 max-w-sm">Tandai pemilih sebagai pendukung melalui halaman Data Pemilih.</p>
                                <a href="{{ route('voters.index') }}" class="mt-4 px-5 py-2.5 bg-emerald-600 text-white font-bold rounded-xl text-xs shadow-md">Buka Data Pemilih</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($supporters->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $supporters->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    function removeSupporter(voterId) {
        if (!confirm('Hapus status pendukung untuk pemilih ini?')) return;

        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: `/voters/${voterId}/toggle`,
            type: 'POST',
            data: { _token: csrfToken },
            success: function(response) {
                if (response.success) {
                    $(`#supporter-row-${voterId}`).fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            }
        });
    }
</script>
@endpush
