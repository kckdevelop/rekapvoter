@extends('layouts.app')

@section('title', 'Manajemen Pengguna / Saksi TPS')
@section('page-title', 'Pengguna & Saksi TPS')
@section('page-subtitle', 'Kelola akun petugas saksi tiap TPS dan administrator sistem')

@section('header-actions')
    <a href="{{ route('users.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-sm font-bold shadow-md shadow-emerald-900/20 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Tambah Pengguna</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Total Users --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($totalUsers) }}</p>
            </div>
        </div>

        {{-- Total Saksi TPS --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Petugas Saksi TPS</p>
                <p class="text-2xl font-extrabold text-sky-600 mt-0.5">{{ number_format($totalSaksi) }}</p>
            </div>
        </div>

        {{-- Total Admin --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Administrator</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-0.5">{{ number_format($totalAdmin) }}</p>
            </div>
        </div>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, nomor HP..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
            </div>

            {{-- Role Filter --}}
            <select name="role" class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Role</option>
                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Administrator</option>
                <option value="saksi" {{ $role === 'saksi' ? 'selected' : '' }}>Saksi TPS</option>
            </select>

            {{-- TPS Filter --}}
            <select name="tps_id" class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua TPS</option>
                @foreach($tpsList as $tps)
                    <option value="{{ $tps->id }}" {{ (string)$tpsId === (string)$tps->id ? 'selected' : '' }}>{{ $tps->nama_tps }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-bold transition-colors">
                Filter
            </button>
            @if($search || $role || $tpsId)
                <a href="{{ route('users.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-bold text-center transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- TABLE USERS --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Nama & Akun</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Penugasan TPS</th>
                        <th class="py-3.5 px-4">Kontak / HP</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $userItem)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-4 text-center font-bold text-slate-400 text-xs">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs text-white {{ $userItem->isAdmin() ? 'bg-emerald-600' : 'bg-sky-600' }}">
                                        {{ strtoupper(substr($userItem->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 leading-tight">{{ $userItem->name }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $userItem->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                @if($userItem->isAdmin())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        🛡️ Administrator
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                        📍 Saksi TPS
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @if($userItem->isSaksi() && $userItem->tps)
                                    <span class="font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg text-xs border border-slate-200">
                                        {{ $userItem->tps->nama_tps }}
                                    </span>
                                @elseif($userItem->isSaksi())
                                    <span class="text-rose-500 font-semibold text-xs italic">⚠️ Belum ditentukan TPS</span>
                                @else
                                    <span class="text-slate-400 text-xs font-medium">Semua TPS (Full Akses)</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-slate-600 font-medium text-xs">
                                {{ $userItem->phone ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('users.edit', $userItem) }}"
                                       class="p-2 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if(Auth::id() !== $userItem->id)
                                        <form method="POST" action="{{ route('users.destroy', $userItem) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $userItem->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <p class="font-semibold text-sm">Tidak ada data pengguna ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
