@extends('layouts.app')

@section('title', 'Data Pemilih')
@section('page-title', 'Daftar Pemilih & Penandaan Pendukung')
@section('page-subtitle', 'Kelola data pemilih, edit data, penandaan pendukung (AJAX) dan aksi massal')

@section('header-actions')
    <button @click="$dispatch('open-import-modal')"
            class="inline-flex items-center gap-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold px-4 py-2.5 rounded-xl border border-emerald-200/80 transition-all text-sm shadow-sm cursor-pointer"
            id="btn-import-excel">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        <span>Import Excel</span>
    </button>
    
    <a href="{{ route('voters.create') }}"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-bold px-4 py-2.5 rounded-xl transition-all text-sm shadow-md shadow-emerald-900/20"
       id="btn-tambah-voter">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        <span>Tambah Manual</span>
    </a>
@endsection

@section('content')
<div class="py-4 space-y-5"
     x-data="{ modalImportOpen: false, deleteModalOpen: false, deleteAction: '', deleteName: '' }"
     @open-import-modal.window="modalImportOpen = true"
     @open-voter-delete-modal.window="deleteModalOpen = true; deleteAction = $event.detail.action; deleteName = $event.detail.name">

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

    {{-- ===== FILTER & SEARCH BAR ===== --}}
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('voters.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1" id="filter-form">
            
            {{-- Search Box --}}
            <div class="relative flex-1 min-w-52">
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nama pemilih..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            {{-- Filter TPS --}}
            <div class="relative min-w-44">
                <select name="tps_id" id="tps_id" onchange="this.form.submit()"
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

            {{-- Filter Status Pendukung --}}
            <div class="relative min-w-44">
                <select name="status_supporter" onchange="this.form.submit()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all appearance-none pr-10">
                    <option value="">-- Semua Status --</option>
                    <option value="1" {{ request('status_supporter') === '1' ? 'selected' : '' }}>⭐ Pendukung</option>
                    <option value="0" {{ request('status_supporter') === '0' ? 'selected' : '' }}>Pemilih Biasa</option>
                </select>
                <div class="absolute right-3.5 top-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition-colors">
                Filter
            </button>

            @if($selectedTps || $search || request('status_supporter') !== null)
                <a href="{{ route('voters.index') }}" class="inline-flex items-center gap-1 text-xs text-rose-600 hover:text-rose-800 bg-rose-50 px-3 py-2 rounded-xl font-bold transition-colors">
                    <span>✕ Reset</span>
                </a>
            @endif
        </form>

        <div class="text-xs text-slate-500 font-semibold bg-slate-100 px-3.5 py-2 rounded-xl border border-slate-200/60 self-start md:self-auto">
            Total: <span class="font-extrabold text-emerald-700 text-sm ml-1">{{ $voters->total() }}</span> pemilih
        </div>
    </div>

    {{-- ===== FLOATING BULK ACTION BAR ===== --}}
    <div id="bulk-action-bar"
         class="hidden bg-slate-900 text-white rounded-2xl p-4 shadow-2xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 animate-fade-in">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-extrabold text-xs">
                <span id="selected-count">0</span>
            </div>
            <span class="text-xs font-bold text-slate-200">Pemilih dipilih secara massal</span>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <button type="button" onclick="executeBulkSupporter(1)"
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold px-3.5 py-2 rounded-xl text-xs transition-all shadow-md">
                <span>⭐ Jadi Pendukung</span>
            </button>
            <button type="button" onclick="executeBulkSupporter(0)"
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-extrabold px-3.5 py-2 rounded-xl text-xs transition-all border border-slate-700">
                <span>✕ Jadi Biasa</span>
            </button>
            <button type="button" onclick="executeBulkDelete()"
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 bg-rose-600 hover:bg-rose-500 text-white font-extrabold px-3.5 py-2 rounded-xl text-xs transition-all shadow-md">
                <span>🗑️ Hapus Terpilih</span>
            </button>
        </div>
    </div>

    {{-- ===== VOTERS TABLE CONTAINER ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse min-w-[750px]">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200 text-slate-500 text-[11px] font-extrabold uppercase tracking-wider">
                        {{-- Select Multi Checkbox Header --}}
                        <th class="px-4 py-4 w-12 text-center">
                            <input type="checkbox" id="check-all" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                        </th>
                        <th class="px-4 py-4 w-10 text-center">#</th>
                        <th class="px-6 py-4">Nama Pemilih</th>
                        <th class="px-6 py-4">Tempat Pemungutan Suara (TPS)</th>
                        <th class="px-6 py-4 text-center">Status Pendukung</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="voters-table-body">
                    @forelse($voters as $voter)
                    @php $isSupporter = $voter->is_supporter; @endphp
                    <tr id="voter-row-{{ $voter->id }}"
                        class="voter-row transition-all duration-200 {{ $isSupporter ? 'bg-emerald-50/70 hover:bg-emerald-100/60 border-l-4 border-l-emerald-500' : 'hover:bg-slate-50/80' }}">
                        
                        {{-- Select Multi Checkbox --}}
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox"
                                   class="voter-checkbox w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer"
                                   value="{{ $voter->id }}"
                                   onchange="updateBulkBar()">
                        </td>

                        {{-- Iteration --}}
                        <td class="px-4 py-4 text-center text-slate-400 font-semibold text-xs">
                            {{ $loop->iteration + ($voters->currentPage()-1)*$voters->perPage() }}
                        </td>
                        
                        {{-- Nama Pemilih --}}
                        <td class="px-6 py-4">
                            <span id="voter-name-{{ $voter->id }}" class="font-bold text-sm {{ $isSupporter ? 'text-emerald-950' : 'text-slate-800' }}">
                                {{ $voter->nama }}
                            </span>
                        </td>

                        {{-- TPS Badge --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200/80">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $voter->tps->nama_tps }}
                            </span>
                        </td>

                        {{-- Status Toggle Button (Single AJAX) --}}
                        <td class="px-6 py-4 text-center">
                            <button type="button"
                                    onclick="toggleSupporter({{ $voter->id }})"
                                    id="btn-toggle-{{ $voter->id }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-extrabold transition-all duration-200 cursor-pointer shadow-sm border {{ $isSupporter ? 'bg-emerald-600 border-emerald-600 text-white hover:bg-emerald-700 shadow-emerald-900/20' : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-100 hover:border-slate-400' }}">
                                <span id="toggle-icon-{{ $voter->id }}">
                                    @if($isSupporter)
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    @else
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    @endif
                                </span>
                                <span id="toggle-label-{{ $voter->id }}">
                                    {{ $isSupporter ? 'Pendukung ✓' : 'Biasa' }}
                                </span>
                            </button>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('voters.edit', $voter) }}"
                                   class="inline-flex items-center gap-1 text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold px-3 py-1.5 rounded-lg border border-amber-200/60 transition-colors"
                                   id="btn-edit-voter-{{ $voter->id }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>

                                <button type="button"
                                        @click="$dispatch('open-voter-delete-modal', { action: '{{ route('voters.destroy', $voter) }}', name: '{{ addslashes($voter->nama) }}' })"
                                        class="inline-flex items-center gap-1 text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-800 font-bold px-3 py-1.5 rounded-lg border border-rose-200/60 transition-colors cursor-pointer"
                                        id="btn-delete-voter-{{ $voter->id }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <p class="text-slate-700 font-extrabold text-base">Tidak Ada Data Pemilih</p>
                                <p class="text-slate-400 text-xs mt-1 max-w-sm">Tambahkan data pemilih manual atau gunakan fitur Import Excel.</p>
                                <div class="flex gap-3 mt-5">
                                    <button @click="modalImportOpen = true" class="px-4 py-2 bg-emerald-50 text-emerald-700 font-bold rounded-xl text-xs">Import Excel</button>
                                    <a href="{{ route('voters.create') }}" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl text-xs">Tambah Pemilih</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($voters->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $voters->links() }}
        </div>
        @endif
    </div>

    {{-- ===== MODAL IMPORT EXCEL ===== --}}
    <div x-show="modalImportOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md"
         style="display: none;">
        <div @click.away="modalImportOpen = false"
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-lg p-6 space-y-5">

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Import Data Pemilih</h3>
                        <p class="text-slate-400 text-xs">Ikuti 3 langkah di bawah ini</p>
                    </div>
                </div>
                <button @click="modalImportOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Step 1: Download Template --}}
            <div class="bg-sky-50 border border-sky-200 rounded-2xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <p class="font-extrabold text-sky-900 text-xs flex items-center gap-1.5 mb-1">
                            <span class="w-5 h-5 bg-sky-600 text-white rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0">1</span>
                            Download Template Excel
                        </p>
                        <p class="text-sky-700 text-xs leading-relaxed ml-6">
                            Isi kolom <code class="bg-sky-100 text-sky-900 px-1.5 py-0.5 rounded font-bold">nama</code> dengan nama-nama pemilih. Satu baris = satu pemilih.
                        </p>
                        {{-- Preview tabel --}}
                        <div class="ml-6 mt-2 border border-sky-200 rounded-xl overflow-hidden text-xs">
                            <div class="bg-sky-600 text-white font-extrabold px-3 py-1.5 tracking-wide">nama</div>
                            <div class="bg-white divide-y divide-sky-100">
                                <div class="px-3 py-1 text-slate-600">Ahmad Subagyo</div>
                                <div class="px-3 py-1 text-slate-600">Siti Rahayu</div>
                                <div class="px-3 py-1 text-slate-400 italic">... dst</div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('voters.template') }}" download="template_import_pemilih.xlsx"
                       class="inline-flex items-center gap-1.5 bg-sky-600 hover:bg-sky-700 text-white font-extrabold text-xs px-3.5 py-2.5 rounded-xl transition-colors shadow-sm whitespace-nowrap flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download (.xlsx)
                    </a>
                </div>
            </div>

            {{-- Step 2 & 3: Form Upload --}}
            <form action="{{ route('voters.import') }}" method="POST" enctype="multipart/form-data" id="form-import-excel">
                @csrf
                <div class="space-y-4">

                    {{-- Step 2: Pilih TPS --}}
                    <div class="border border-slate-200 rounded-2xl p-4 space-y-2">
                        <p class="font-extrabold text-slate-800 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 bg-slate-700 text-white rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0">2</span>
                            Pilih TPS Tujuan <span class="text-rose-500">*</span>
                        </p>
                        <div class="relative">
                            <select name="tps_id" id="import_tps_id" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all appearance-none pr-10">
                                <option value="">-- Pilih TPS --</option>
                                @foreach($tpsList as $tps)
                                    <option value="{{ $tps->id }}">{{ $tps->nama_tps }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-3.5 top-2.5 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        @error('tps_id')
                            <p class="text-rose-500 text-xs font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Step 3: Pilih File --}}
                    <div class="border border-slate-200 rounded-2xl p-4 space-y-2">
                        <p class="font-extrabold text-slate-800 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 bg-slate-700 text-white rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0">3</span>
                            Pilih File Excel <span class="text-rose-500">*</span>
                        </p>
                        <label for="import_file"
                               class="flex flex-col items-center justify-center gap-2 w-full h-24 bg-slate-50 hover:bg-emerald-50 border-2 border-dashed border-slate-300 hover:border-emerald-400 rounded-xl cursor-pointer transition-all group">
                            <svg class="w-7 h-7 text-slate-300 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <span class="text-xs font-bold text-slate-400 group-hover:text-emerald-600 transition-colors" id="import-file-label">Klik untuk pilih .xlsx / .xls / .csv</span>
                            <input type="file" name="file" id="import_file" accept=".xlsx,.xls,.csv" required class="hidden"
                                   onchange="document.getElementById('import-file-label').textContent = this.files[0]?.name || 'Klik untuk pilih file'">
                        </label>
                        @error('file')
                            <p class="text-rose-500 text-xs font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                        <button type="button" @click="modalImportOpen = false"
                                class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-sm hover:bg-slate-200 transition-colors">Batal</button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm transition-colors shadow-md shadow-emerald-900/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unggah &amp; Import
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL KONFIRMASI HAPUS PEMILIH ===== --}}
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
                    <h3 class="font-extrabold text-slate-900 text-base">Hapus Data Pemilih?</h3>
                    <p class="text-slate-500 text-sm mt-1">
                        Anda akan menghapus data pemilih <strong class="text-slate-900" x-text="deleteName"></strong> secara permanen dari sistem.
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
                        Ya, Hapus Pemilih
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    /**
     * Check-all / Select Multi Checkbox handler
     */
    $('#check-all').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.voter-checkbox').prop('checked', isChecked);
        updateBulkBar();
    });

    function updateBulkBar() {
        const selectedCount = $('.voter-checkbox:checked').length;
        $('#selected-count').text(selectedCount);

        if (selectedCount > 0) {
            $('#bulk-action-bar').removeClass('hidden').addClass('flex');
        } else {
            $('#bulk-action-bar').addClass('hidden').removeClass('flex');
            $('#check-all').prop('checked', false);
        }
    }

    /**
     * Execute Bulk Update Status (is_supporter = 1 or 0)
     */
    function executeBulkSupporter(status) {
        const selectedIds = [];
        $('.voter-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) return;

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/voters/bulk-supporter',
            type: 'POST',
            data: {
                _token: csrfToken,
                voter_ids: selectedIds,
                is_supporter: status
            },
            success: function(response) {
                if (response.success) {
                    const isSupporter = response.is_supporter;
                    
                    selectedIds.forEach(id => {
                        const $row = $(`#voter-row-${id}`);
                        const $name = $(`#voter-name-${id}`);
                        const $btn = $(`#btn-toggle-${id}`);
                        const $label = $(`#toggle-label-${id}`);
                        const $icon = $(`#toggle-icon-${id}`);

                        if (isSupporter) {
                            $row.removeClass('hover:bg-slate-50/80')
                                .addClass('bg-emerald-50/70 hover:bg-emerald-100/60 border-l-4 border-l-emerald-500');

                            $name.removeClass('text-slate-800').addClass('text-emerald-950');

                            $btn.removeClass('bg-white border-slate-300 text-slate-600 hover:bg-slate-100 hover:border-slate-400')
                                .addClass('bg-emerald-600 border-emerald-600 text-white hover:bg-emerald-700 shadow-emerald-900/20');

                            $label.text('Pendukung ✓');
                            $icon.html('<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>');
                        } else {
                            $row.removeClass('bg-emerald-50/70 hover:bg-emerald-100/60 border-l-4 border-l-emerald-500')
                                .addClass('hover:bg-slate-50/80');

                            $name.removeClass('text-emerald-950').addClass('text-slate-800');

                            $btn.removeClass('bg-emerald-600 border-emerald-600 text-white hover:bg-emerald-700 shadow-emerald-900/20')
                                .addClass('bg-white border-slate-300 text-slate-600 hover:bg-slate-100 hover:border-slate-400');

                            $label.text('Biasa');
                            $icon.html('<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>');
                        }
                    });

                    $('.voter-checkbox, #check-all').prop('checked', false);
                    updateBulkBar();
                }
            },
            error: function() {
                alert('Gagal memperbarui status secara massal.');
            }
        });
    }

    /**
     * Execute Bulk Delete Voters
     */
    function executeBulkDelete() {
        const selectedIds = [];
        $('.voter-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) return;

        if (!confirm(`Hapus ${selectedIds.length} data pemilih yang dipilih?`)) return;

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/voters/bulk-delete',
            type: 'POST',
            data: {
                _token: csrfToken,
                voter_ids: selectedIds
            },
            success: function(response) {
                if (response.success) {
                    selectedIds.forEach(id => {
                        $(`#voter-row-${id}`).fadeOut(300, function() {
                            $(this).remove();
                        });
                    });
                    $('.voter-checkbox, #check-all').prop('checked', false);
                    updateBulkBar();
                }
            },
            error: function() {
                alert('Gagal menghapus data pemilih terpilih.');
            }
        });
    }

    /**
     * AJAX Toggle status is_supporter (Single)
     */
    function toggleSupporter(voterId) {
        const url = `/voters/${voterId}/toggle`;
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        const $btn = $(`#btn-toggle-${voterId}`);
        $btn.prop('disabled', true).addClass('opacity-50');

        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: csrfToken },
            success: function(response) {
                if (response.success) {
                    const isSupporter = response.is_supporter;
                    const $row = $(`#voter-row-${voterId}`);
                    const $name = $(`#voter-name-${voterId}`);
                    const $label = $(`#toggle-label-${voterId}`);
                    const $icon = $(`#toggle-icon-${voterId}`);

                    if (isSupporter) {
                        $row.removeClass('hover:bg-slate-50/80')
                            .addClass('bg-emerald-50/70 hover:bg-emerald-100/60 border-l-4 border-l-emerald-500');

                        $name.removeClass('text-slate-800').addClass('text-emerald-950');

                        $btn.removeClass('bg-white border-slate-300 text-slate-600 hover:bg-slate-100 hover:border-slate-400')
                            .addClass('bg-emerald-600 border-emerald-600 text-white hover:bg-emerald-700 shadow-emerald-900/20');

                        $label.text('Pendukung ✓');
                        $icon.html('<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>');
                    } else {
                        $row.removeClass('bg-emerald-50/70 hover:bg-emerald-100/60 border-l-4 border-l-emerald-500')
                            .addClass('hover:bg-slate-50/80');

                        $name.removeClass('text-emerald-950').addClass('text-slate-800');

                        $btn.removeClass('bg-emerald-600 border-emerald-600 text-white hover:bg-emerald-700 shadow-emerald-900/20')
                            .addClass('bg-white border-slate-300 text-slate-600 hover:bg-slate-100 hover:border-slate-400');

                        $label.text('Biasa');
                        $icon.html('<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>');
                    }
                }
            },
            error: function() {
                alert('Gagal mengubah status. Silakan coba lagi.');
            },
            complete: function() {
                $btn.prop('disabled', false).removeClass('opacity-50');
            }
        });
    }
</script>
@endpush
