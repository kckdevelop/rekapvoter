@extends('layouts.app')

@section('title', 'Data Calon Lurah')
@section('page-title', 'Pengaturan Data Calon Lurah (Paslon)')
@section('page-subtitle', 'Kelola daftar calon lurah utama dan calon lawan untuk form input hasil suara real TPS')

@section('header-actions')
    <button @click="$dispatch('open-candidate-create-modal')"
            class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-extrabold px-4 py-2.5 rounded-xl transition-all text-sm shadow-md shadow-emerald-900/20 cursor-pointer"
            id="btn-tambah-candidate">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        <span>+ Tambah Calon Lawan</span>
    </button>
@endsection

@section('content')
<div class="py-4 space-y-6"
     x-data="{ createModalOpen: false, editModalOpen: false, editId: null, editNo: '', editName: '', editWarna: '#2563eb', deleteModalOpen: false, deleteAction: '', deleteName: '' }"
     @open-candidate-create-modal.window="createModalOpen = true"
     @open-candidate-edit-modal.window="
        editModalOpen = true;
        editId = $event.detail.id;
        editNo = $event.detail.no;
        editName = $event.detail.name;
        editWarna = $event.detail.warna;
     "
     @open-candidate-delete-modal.window="
        deleteModalOpen = true;
        deleteAction = $event.detail.action;
        deleteName = $event.detail.name;
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
                <svg class="w-5 h-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">✕</button>
        </div>
    @endif

    {{-- Info Card --}}
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-3xl p-6 shadow-xl border border-slate-800 flex items-center justify-between gap-6">
        <div class="space-y-1">
            <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
                💡 Pengaturan Form Real Count
            </span>
            <h3 class="text-lg font-extrabold tracking-tight mt-2">Daftar Calon Lurah Terdaftar ({{ $candidates->count() }} Paslon)</h3>
            <p class="text-slate-400 text-xs leading-relaxed">
                Form input real count pada setiap TPS akan <strong>otomatis menyesuaikan</strong> jumlah kolom input sesuai daftar calon lawan yang Anda tambahkan di halaman ini.
            </p>
        </div>
    </div>

    {{-- Candidates Table --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200 text-slate-500 text-[11px] font-extrabold uppercase tracking-wider">
                        <th class="px-6 py-4 w-20 text-center">No Urut</th>
                        <th class="px-6 py-4">Nama Calon Lurah</th>
                        <th class="px-6 py-4 text-center">Peran</th>
                        <th class="px-6 py-4 text-center">Warna Label</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($candidates as $candidate)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-2xl font-black text-sm {{ $candidate->is_main_candidate ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/30' : 'bg-slate-800 text-white' }}">
                                {{ sprintf('%02d', $candidate->nomor_urut) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-extrabold text-slate-900 text-base block">{{ $candidate->nama }}</span>
                            <span class="text-xs text-slate-400 font-medium">Calon Lurah Sabdodadi No. {{ $candidate->nomor_urut }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($candidate->is_main_candidate)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm">
                                    ⭐ Kandidat Utama
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    👥 Calon Lawan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="w-4 h-4 rounded-full inline-block shadow-sm" style="background-color: {{ $candidate->warna_badge }}"></span>
                                <span class="text-xs font-mono font-bold text-slate-600">{{ strtoupper($candidate->warna_badge) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                        @click="$dispatch('open-candidate-edit-modal', {
                                            id: {{ $candidate->id }},
                                            no: {{ $candidate->nomor_urut }},
                                            name: '{{ addslashes($candidate->nama) }}',
                                            warna: '{{ $candidate->warna_badge }}'
                                        })"
                                        class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold px-3 py-1.5 rounded-xl border border-amber-200/60 transition-colors cursor-pointer"
                                        id="btn-edit-candidate-{{ $candidate->id }}">
                                    Edit
                                </button>

                                @if(!$candidate->is_main_candidate)
                                    <button type="button"
                                            @click="$dispatch('open-candidate-delete-modal', {
                                                action: '{{ route('candidates.destroy', $candidate) }}',
                                                name: '{{ addslashes($candidate->nama) }}'
                                            })"
                                            class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold px-3 py-1.5 rounded-xl border border-rose-200/60 transition-colors cursor-pointer"
                                            id="btn-delete-candidate-{{ $candidate->id }}">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== MODAL TAMBAH CALON LAWAN ===== --}}
    <div x-show="createModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md"
         style="display: none;">

        <div @click.away="createModalOpen = false"
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md p-6 space-y-5">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        👤
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Tambah Calon Lawan</h3>
                        <p class="text-slate-400 text-xs">Masukkan nomor urut dan nama calon lawan</p>
                    </div>
                </div>
                <button @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">✕</button>
            </div>

            <form action="{{ route('candidates.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="create_nomor_urut" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Nomor Urut <span class="text-rose-500">*</span></label>
                    <input type="number" id="create_nomor_urut" name="nomor_urut" required min="1" placeholder="Contoh: 2, 3, dst"
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                </div>

                <div>
                    <label for="create_nama" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap Calon Lawan <span class="text-rose-500">*</span></label>
                    <input type="text" id="create_nama" name="nama" required placeholder="Contoh: Drs. H. Subagyo, M.Si"
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                </div>

                <div>
                    <label for="create_warna_badge" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Pilih Warna Label</label>
                    <input type="color" id="create_warna_badge" name="warna_badge" value="#2563eb"
                           class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl p-1 cursor-pointer">
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-extrabold text-xs hover:bg-emerald-700 shadow-md shadow-emerald-900/20">
                        Simpan Calon Lawan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL EDIT CALON LAWAN ===== --}}
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
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md p-6 space-y-5">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        ✏️
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Edit Data Calon</h3>
                        <p class="text-slate-400 text-xs">Ubah informasi nama &amp; nomor urut</p>
                    </div>
                </div>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">✕</button>
            </div>

            <form :action="'/candidates/' + editId" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="edit_nomor_urut" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Nomor Urut <span class="text-rose-500">*</span></label>
                    <input type="number" id="edit_nomor_urut" name="nomor_urut" x-model="editNo" required min="1"
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                </div>

                <div>
                    <label for="edit_nama" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap Calon <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_nama" name="nama" x-model="editName" required
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                </div>

                <div>
                    <label for="edit_warna_badge" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Pilih Warna Label</label>
                    <input type="color" id="edit_warna_badge" name="warna_badge" x-model="editWarna"
                           class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl p-1 cursor-pointer">
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

    {{-- ===== MODAL KONFIRMASI HAPUS CALON LAWAN ===== --}}
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
                    <h3 class="font-extrabold text-slate-900 text-base">Hapus Calon Lawan?</h3>
                    <p class="text-slate-500 text-sm mt-1">
                        Anda akan menghapus data calon lawan <strong class="text-slate-900" x-text="deleteName"></strong>.
                    </p>
                </div>
            </div>

            <form :action="deleteAction" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <button type="button" @click="deleteModalOpen = false" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-sm hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-sm shadow-md shadow-rose-900/20">
                        Ya, Hapus Calon
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
