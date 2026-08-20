@extends('layouts.app')

@section('title', 'Tambah TPS')
@section('page-title', 'Tambah TPS Baru')
@section('page-subtitle', 'Daftarkan Tempat Pemungutan Suara baru')

@section('header-actions')
    <a href="{{ route('tps.index') }}"
       class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2.5 rounded-xl transition-colors text-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali</span>
    </a>
@endsection

@section('content')
<div class="py-6 max-w-xl">
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
        
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-base font-extrabold text-slate-900">Informasi TPS</h2>
            <p class="text-slate-400 text-xs mt-0.5">Masukkan nama lokasi Tempat Pemungutan Suara baru</p>
        </div>

        <form action="{{ route('tps.store') }}" method="POST" id="form-tambah-tps" class="space-y-5">
            @csrf
            
            {{-- Nama TPS Input --}}
            <div class="space-y-1.5">
                <label for="nama_tps" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                    Nama TPS <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama_tps" name="nama_tps"
                       value="{{ old('nama_tps') }}"
                       placeholder="Contoh: TPS 01, TPS 02"
                       class="w-full bg-slate-50 border border-slate-200/90 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all @error('nama_tps') border-rose-400 bg-rose-50/30 @enderror"
                       autocomplete="off">
                @error('nama_tps')
                    <p class="text-rose-500 text-xs font-semibold mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-extrabold px-6 py-3 rounded-2xl transition-all text-xs shadow-md shadow-emerald-900/20"
                        id="btn-simpan-tps">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan TPS</span>
                </button>
                
                <a href="{{ route('tps.index') }}"
                   class="inline-flex items-center justify-center px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
