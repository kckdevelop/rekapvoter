@extends('layouts.app')

@section('title', 'Edit Pemilih')
@section('page-title', 'Edit Data Pemilih')
@section('page-subtitle', 'Perbarui informasi pemilih dan status pendukung')

@section('header-actions')
    <a href="{{ route('voters.index') }}"
       class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2.5 rounded-xl transition-colors text-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali</span>
    </a>
@endsection

@section('content')
<div class="py-6 max-w-xl">
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
        
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-base font-extrabold text-slate-900">Perbarui Data Pemilih</h2>
            <p class="text-slate-400 text-xs mt-0.5">Ubah nama, alokasi TPS, atau status pendukung</p>
        </div>

        <form action="{{ route('voters.update', $voter) }}" method="POST" id="form-edit-voter" class="space-y-5">
            @csrf
            @method('PUT')
            
            {{-- Dropdown TPS --}}
            <div class="space-y-1.5">
                <label for="tps_id" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                    Tempat Pemungutan Suara (TPS) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select id="tps_id" name="tps_id"
                            class="w-full bg-slate-50 border border-slate-200/90 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all appearance-none pr-10 @error('tps_id') border-rose-400 bg-rose-50/30 @enderror">
                        <option value="">-- Pilih TPS --</option>
                        @foreach($tpsList as $tps)
                            <option value="{{ $tps->id }}" {{ old('tps_id', $voter->tps_id) == $tps->id ? 'selected' : '' }}>
                                {{ $tps->nama_tps }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-3.5 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                @error('tps_id')
                    <p class="text-rose-500 text-xs font-semibold mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Nama Pemilih Input --}}
            <div class="space-y-1.5">
                <label for="nama" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                    Nama Pemilih <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama" name="nama"
                       value="{{ old('nama', $voter->nama) }}"
                       placeholder="Contoh: Ahmad Subagyo"
                       class="w-full bg-slate-50 border border-slate-200/90 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all @error('nama') border-rose-400 bg-rose-50/30 @enderror"
                       autocomplete="off">
                @error('nama')
                    <p class="text-rose-500 text-xs font-semibold mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Status Pendukung Checkbox --}}
            <div class="pt-2">
                <label for="is_supporter" class="inline-flex items-center gap-3 p-3.5 bg-emerald-50/70 border border-emerald-200/80 rounded-2xl cursor-pointer w-full hover:bg-emerald-100/50 transition-colors">
                    <input type="checkbox" id="is_supporter" name="is_supporter" value="1"
                           {{ old('is_supporter', $voter->is_supporter) ? 'checked' : '' }}
                           class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <div>
                        <p class="text-xs font-extrabold text-emerald-950">Tandai Sebagai Pendukung</p>
                        <p class="text-[11px] text-emerald-700 font-medium">Pemilih ini akan masuk ke dalam rekapitulasi pendukung Nurma Setiawan, SE</p>
                    </div>
                </label>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-extrabold px-6 py-3 rounded-2xl transition-all text-xs shadow-md shadow-emerald-900/20"
                        id="btn-update-voter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
                
                <a href="{{ route('voters.index') }}"
                   class="inline-flex items-center justify-center px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
