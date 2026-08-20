@extends('layouts.app')

@section('title', 'Manajemen TPS')
@section('page-title', 'Manajemen TPS')
@section('page-subtitle', 'Daftar Tempat Pemungutan Suara terdaftar di Kalurahan Sabdodadi')

@section('header-actions')
    <button @click="$dispatch('open-tps-create-modal')"
            class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-extrabold px-4 py-2.5 rounded-xl transition-all text-sm shadow-md shadow-emerald-900/20 cursor-pointer"
            id="btn-tambah-tps">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        <span>+ Tambah TPS</span>
    </button>
@endsection

@section('content')
<div class="py-4 space-y-6"
     x-data="{ createModalOpen: false, editModalOpen: false, editId: null, editName: '', deleteModalOpen: false, deleteAction: '', deleteName: '' }"
     @open-tps-create-modal.window="createModalOpen = true"
     @open-tps-edit-modal.window="editModalOpen = true; editId = $event.detail.id; editName = $event.detail.name"
     @open-tps-delete-modal.window="deleteModalOpen = true; deleteAction = $event.detail.action; deleteName = $event.detail.name">

    {{-- Alert Flash Notification --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-sm font-bold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
    @endif

    {{-- ===== KPI STATS CARDS ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-xl">
                📍
            </div>
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Total TPS</p>
                <h3 class="text-2xl font-black text-slate-900 mt-0.5">{{ number_format($totalTpsCount) }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xl">
                👥
            </div>
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Total Pemilih</p>
                <h3 class="text-2xl font-black text-slate-900 mt-0.5">{{ number_format($totalVotersCount) }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xl">
                ⭐
            </div>
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Pendukung</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-0.5">{{ number_format($totalSupportersCount) }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xl">
                📊
            </div>
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">% Pendukung</p>
                <h3 class="text-2xl font-black text-amber-700 mt-0.5">{{ $overallPercentage }}%</h3>
            </div>
        </div>
    </div>

    {{-- ===== SEARCH & FILTER BAR ===== --}}
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('tps.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-64">
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nama TPS (misal: TPS 01)..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition-colors">
                Cari TPS
            </button>

            @if($search)
                <a href="{{ route('tps.index') }}" class="inline-flex items-center gap-1 text-xs text-rose-600 hover:text-rose-800 bg-rose-50 px-3 py-2 rounded-xl font-bold transition-colors">
                    <span>✕ Reset</span>
                </a>
            @endif
        </form>
    </div>

    {{-- ===== TABLE TPS ===== --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200 text-slate-500 text-[11px] font-extrabold uppercase tracking-wider">
                        <th class="px-6 py-4 w-12 text-center">#</th>
                        <th class="px-6 py-4">Nama TPS</th>
                        <th class="px-6 py-4 text-center">Total Pemilih</th>
                        <th class="px-6 py-4 text-center">Jumlah Pendukung</th>
                        <th class="px-6 py-4 text-center">% Pendukung</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tpsList as $tps)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-center text-slate-400 font-semibold text-xs">
                            {{ $loop->iteration + ($tpsList->currentPage()-1)*$tpsList->perPage() }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-extrabold text-slate-900 text-sm">{{ $tps->nama_tps }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-xl text-xs border border-slate-200/60">
                                {{ number_format($tps->voters_count) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                {{ number_format($tps->supporters_count) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($tps->voters_count > 0)
                                @php $pct = round(($tps->supporters_count/$tps->voters_count)*100, 1); @endphp
                                <div class="flex items-center justify-center gap-2.5">
                                    <div class="w-20 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/60">
                                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-slate-800 font-extrabold text-xs">{{ $pct }}%</span>
                                </div>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('voters.index', ['tps_id' => $tps->id]) }}"
                                   class="text-xs bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold px-3 py-1.5 rounded-xl border border-sky-200/60 transition-colors">
                                    Lihat Pemilih
                                </a>
                                
                                <button type="button"
                                        @click="$dispatch('open-tps-edit-modal', { id: {{ $tps->id }}, name: '{{ addslashes($tps->nama_tps) }}' })"
                                        class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold px-3 py-1.5 rounded-xl border border-amber-200/60 transition-colors cursor-pointer"
                                        id="btn-edit-tps-{{ $tps->id }}">
                                    Edit
                                </button>

                                <button type="button"
                                        @click="$dispatch('open-tps-delete-modal', { action: '{{ route('tps.destroy', $tps) }}', name: '{{ addslashes($tps->nama_tps) }}' })"
                                        class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold px-3 py-1.5 rounded-xl border border-rose-200/60 transition-colors cursor-pointer"
                                        id="btn-delete-tps-{{ $tps->id }}">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-300">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                </div>
                                <p class="text-slate-700 font-extrabold text-base">Belum Ada TPS Terdaftar</p>
                                <button @click="createModalOpen = true" class="mt-4 px-5 py-2.5 bg-emerald-600 text-white font-bold rounded-xl text-xs shadow-md">Tambah TPS Pertama</button>
                            </div>
                        </td>
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

    {{-- ===== MODAL TAMBAH TPS ===== --}}
    <div x-show="createModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md"
         style="display: none;">

        <div @click.away="createModalOpen = false"
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md p-6 space-y-5 transform transition-all">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        📍
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Tambah TPS Baru</h3>
                        <p class="text-slate-400 text-xs">Masukkan nama TPS baru ke sistem</p>
                    </div>
                </div>
                <button @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">✕</button>
            </div>

            <form action="{{ route('tps.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="modal_nama_tps" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Nama TPS <span class="text-rose-500">*</span></label>
                    <input type="text" id="modal_nama_tps" name="nama_tps" required placeholder="Contoh: TPS 05"
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-extrabold text-xs hover:bg-emerald-700 shadow-md shadow-emerald-900/20">
                        Simpan TPS
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL EDIT TPS ===== --}}
    <div x-show="editModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md"
         style="display: none;">

        <div @click.away="editModalOpen = false"
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md p-6 space-y-5 transform transition-all">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        ✏️
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Edit TPS</h3>
                        <p class="text-slate-400 text-xs">Perbarui nama TPS terdaftar</p>
                    </div>
                </div>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">✕</button>
            </div>

            <form :action="'/tps/' + editId" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="modal_edit_nama_tps" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Nama TPS <span class="text-rose-500">*</span></label>
                    <input type="text" id="modal_edit_nama_tps" name="nama_tps" x-model="editName" required placeholder="Contoh: TPS 01"
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 text-white font-extrabold text-xs hover:bg-amber-700 shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL KONFIRMASI HAPUS TPS ===== --}}
    <div x-show="deleteModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md"
         style="display: none;">

        <div @click.away="deleteModalOpen = false"
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md p-6 space-y-5">

            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-extrabold text-slate-900 text-base">Hapus TPS?</h3>
                    <p class="text-slate-500 text-sm mt-1">
                        Anda akan menghapus <strong class="text-slate-900" x-text="'TPS ' + deleteName"></strong>.
                        Semua data pemilih di TPS ini juga akan ikut terhapus secara permanen.
                    </p>
                    <div class="mt-3 p-3 bg-rose-50 border border-rose-200 rounded-xl">
                        <p class="text-rose-800 text-xs font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            Tindakan ini tidak dapat dibatalkan!
                        </p>
                    </div>
                </div>
            </div>

            <form :action="deleteAction" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <button type="button" @click="deleteModalOpen = false"
                            class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-sm hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-sm transition-colors shadow-md shadow-rose-900/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Ya, Hapus TPS
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
