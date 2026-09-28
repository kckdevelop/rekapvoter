@extends('layouts.app')

@section('title', 'Tambah Pengguna / Saksi TPS')
@section('page-title', 'Tambah Pengguna Baru')
@section('page-subtitle', 'Buat akun saksi TPS atau administrator sistem')

@section('header-actions')
    <a href="{{ route('users.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali</span>
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto" x-data="{ role: '{{ old('role', 'saksi') }}' }">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h2 class="font-extrabold text-base text-slate-900">Formulir Pendaftaran Akun</h2>
            <p class="text-xs text-slate-500 mt-0.5">Tentukan role dan TPS yang akan dikelola oleh akun ini.</p>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-6">
            @csrf

            {{-- Role Selection --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Role / Peran Pengguna <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
                           :class="role === 'saksi' ? 'border-sky-500 bg-sky-50/40 text-sky-900' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                        <input type="radio" name="role" value="saksi" x-model="role" class="text-sky-600 focus:ring-sky-500">
                        <div>
                            <p class="font-extrabold text-sm">📍 Petugas Saksi TPS</p>
                            <p class="text-xs text-slate-500 mt-0.5">Hanya dapat mengelola data di 1 TPS yang ditugaskan.</p>
                        </div>
                    </label>

                    <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
                           :class="role === 'admin' ? 'border-emerald-500 bg-emerald-50/40 text-emerald-900' : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                        <input type="radio" name="role" value="admin" x-model="role" class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <p class="font-extrabold text-sm">🛡️ Administrator</p>
                            <p class="text-xs text-slate-500 mt-0.5">Akses penuh ke semua data TPS, pemilih, calon, & akun.</p>
                        </div>
                    </label>
                </div>
                @error('role')
                    <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- TPS Assignment (hanya untuk saksi) --}}
            <div x-show="role === 'saksi'" x-transition class="p-4 rounded-xl bg-sky-50/50 border border-sky-200 space-y-2">
                <label for="tps_id" class="block text-xs font-bold uppercase tracking-wider text-sky-900">Penugasan TPS <span class="text-rose-500">*</span></label>
                <select id="tps_id" name="tps_id"
                        class="w-full px-4 py-2.5 bg-white border border-sky-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">-- Pilih TPS yang ditugaskan --</option>
                    @foreach($tpsList as $tps)
                        <option value="{{ $tps->id }}" {{ old('tps_id') == $tps->id ? 'selected' : '' }}>
                            {{ $tps->nama_tps }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-sky-700">Petugas saksi ini akan langsung terhubung dan hanya mengelola data di TPS ini.</p>
                @error('tps_id')
                    <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama Lengkap --}}
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Ahmad Fadli"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                @error('name')
                    <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Email / Username Login --}}
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email / Username Login <span class="text-rose-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="saksi.tps01@gmail.com"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    @error('email')
                        <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone / No. HP --}}
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nomor HP / WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="081234567890"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    @error('phone')
                        <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Password <span class="text-rose-500">*</span></label>
                    <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    @error('password')
                        <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Konfirmasi Password <span class="text-rose-500">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md shadow-emerald-900/20 transition-all">
                    Simpan Akun Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
