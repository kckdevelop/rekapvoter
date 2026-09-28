<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Rekap Data Pendukung Calon Kepala Desa per TPS">
    <title>@yield('title', 'Dashboard') — Hitung Suara</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        emerald: { 50:'#ecfdf5', 100:'#d1fae5', 200:'#a7f3d0', 300:'#6ee7b7', 400:'#34d399', 500:'#10b981', 600:'#059669', 700:'#047857', 800:'#065f46', 900:'#064e3b' },
                        slate:   { 850: '#0f172a', 900: '#0b1329', 950: '#060b19' }
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @stack('styles')

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Custom scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        aside ::-webkit-scrollbar-thumb { background: #334155; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased h-full overflow-hidden" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden">

    {{-- ===== MOBILE BACKDROP ===== --}}
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-40 lg:hidden"
         style="display: none;"></div>

    {{-- ===== SIDEBAR ===== --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-slate-800 shadow-2xl">
        
        {{-- Sidebar Brand Logo --}}
        <div class="px-6 py-5 border-b border-slate-800/80 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 group">
                <div class="w-10 h-10 bg-gradient-to-tr from-emerald-600 to-teal-400 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-900/50 group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-white font-extrabold text-base tracking-tight leading-tight group-hover:text-emerald-400 transition-colors">Hitung Suara</h1>
                    <p class="text-slate-400 text-xs font-medium">Rekap Pendukung Kades</p>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Sidebar Navigation --}}
        <nav class="flex-1 px-4 py-6 space-y-6 overflow-y-auto">
            
            {{-- Menu Utama Section --}}
            <div>
                <p class="text-emerald-400 text-[11px] font-extrabold uppercase tracking-widest px-3 mb-3">Menu Utama</p>
                <div class="space-y-1.5">

                    {{-- Dashboard --}}
                    @php $active = request()->routeIs('dashboard'); @endphp
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-emerald-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span>Dashboard</span>
                    </a>

                    @if(Auth::user()->isAdmin())
                    {{-- Manajemen TPS (Khusus Admin) --}}
                    @php $active = request()->routeIs('tps.*'); @endphp
                    <a href="{{ route('tps.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-sky-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span>Manajemen TPS</span>
                    </a>
                    @endif

                    {{-- Data Pemilih --}}
                    @php $active = request()->routeIs('voters.*'); @endphp
                    <a href="{{ route('voters.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-indigo-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span>{{ Auth::user()->isSaksi() ? 'Data Pemilih TPS' : 'Data Pemilih (DPT)' }}</span>
                    </a>

                    {{-- Khusus Pendukung --}}
                    @php $active = request()->routeIs('supporters.*'); @endphp
                    <a href="{{ route('supporters.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-amber-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </div>
                        <span class="flex-1">Daftar Pendukung</span>
                    </a>

                    {{-- Laporan & Rekap --}}
                    @php $active = request()->routeIs('laporan.*'); @endphp
                    <a href="{{ route('laporan.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-teal-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span>Laporan & Rekap DPT</span>
                    </a>

                </div>
            </div>

            {{-- Quick Count Section --}}
            <div>
                <p class="text-amber-400 text-[11px] font-extrabold uppercase tracking-widest px-3 mb-3">Quick Count (Hitung Cepat)</p>
                <div class="space-y-1.5">

                    {{-- Input Quick Count TPS --}}
                    @php $active = request()->routeIs('quickcount.*'); @endphp
                    <a href="{{ route('quickcount.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-amber-600 to-amber-700 text-white shadow-lg shadow-amber-900/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-amber-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span>{{ Auth::user()->isSaksi() ? 'Input Quick Count TPS' : 'Input Quick Count' }}</span>
                    </a>

                    {{-- Rekap Quick vs Pendukung --}}
                    @php $active = request()->routeIs('rekap-quick.*'); @endphp
                    <a href="{{ route('rekap-quick.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-amber-600 to-amber-700 text-white shadow-lg shadow-amber-900/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-yellow-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="flex-1">Rekap Quick vs Target</span>
                    </a>

                </div>
            </div>

            {{-- Real Count Section --}}
            <div>
                <p class="text-rose-400 text-[11px] font-extrabold uppercase tracking-widest px-3 mb-3">Real Count (Hitung Resmi)</p>
                <div class="space-y-1.5">

                    {{-- Input Hasil Real TPS --}}
                    @php $active = request()->routeIs('realcount.*'); @endphp
                    <a href="{{ route('realcount.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-rose-600 to-rose-700 text-white shadow-lg shadow-rose-900/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-rose-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <span>{{ Auth::user()->isSaksi() ? 'Input Real Count TPS' : 'Input Hasil Real TPS' }}</span>
                    </a>

                    {{-- Rekap Real vs Pendukung --}}
                    @php $active = request()->routeIs('rekap-real.*'); @endphp
                    <a href="{{ route('rekap-real.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-rose-600 to-rose-700 text-white shadow-lg shadow-rose-900/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-rose-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="flex-1">Rekap Real vs Target</span>
                    </a>

                </div>
            </div>

            {{-- Pengaturan Section --}}
            <div>
                <p class="text-slate-500 text-[11px] font-extrabold uppercase tracking-widest px-3 mb-3">Pengaturan</p>
                <div class="space-y-1.5">

                    @if(Auth::user()->isAdmin())
                    {{-- Kelola Akun / User Saksi TPS (Khusus Admin) --}}
                    @php $active = request()->routeIs('users.*'); @endphp
                    <a href="{{ route('users.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-purple-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span>Pengguna / Saksi TPS</span>
                    </a>

                    {{-- Data Calon Lurah (Khusus Admin) --}}
                    @php $active = request()->routeIs('candidates.*'); @endphp
                    <a href="{{ route('candidates.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-emerald-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span>Data Calon (Kandidat)</span>
                    </a>
                    @endif

                    @php $active = request()->routeIs('profile.*'); @endphp
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span>Pengaturan Profil</span>
                    </a>

                </div>
            </div>

        </nav>

        {{-- Sidebar User Profile Card at Bottom --}}
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-900 border border-slate-800">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white font-extrabold text-sm shadow-md">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5">
                        <p class="text-white text-xs font-bold truncate leading-tight">{{ Auth::user()->name ?? 'User' }}</p>
                        <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold {{ Auth::user()->isAdmin() ? 'bg-emerald-500/20 text-emerald-400' : 'bg-sky-500/20 text-sky-400' }}">
                            {{ Auth::user()->isAdmin() ? 'Admin' : 'Saksi' }}
                        </span>
                    </div>
                    <p class="text-slate-400 text-[11px] truncate mt-0.5">
                        @if(Auth::user()->isSaksi() && Auth::user()->tps)
                            📍 {{ Auth::user()->tps->nama_tps }}
                        @else
                            {{ Auth::user()->email ?? 'admin@example.com' }}
                        @endif
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                            class="text-slate-400 hover:text-red-400 p-2 rounded-lg hover:bg-slate-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- ===== MAIN CONTENT WRAPPER ===== --}}
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden bg-slate-50">
        
        {{-- Topbar Header --}}
        <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 lg:px-8 py-4 sticky top-0 z-20">
            <div class="flex items-center justify-between gap-4">
                
                {{-- Left: Mobile Hamburger + Title --}}
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true"
                            class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-lg lg:text-xl font-extrabold text-slate-900 tracking-tight">@yield('page-title', 'Dashboard')</h1>
                        <p class="text-slate-500 text-xs font-medium mt-0.5 hidden sm:block">@yield('page-subtitle', 'Sistem Rekapitulasi Data Pendukung Calon Kepala Desa')</p>
                    </div>
                </div>

                {{-- Right: Header Action Buttons --}}
                <div class="flex items-center gap-2 sm:gap-3">
                    @yield('header-actions')
                </div>
            </div>
        </header>

        {{-- Flash Notification Alerts --}}
        <div class="px-4 lg:px-8 pt-4">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 rounded-2xl text-sm font-semibold shadow-sm mb-2">
                    <div class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span>{{ session('success') }}</span>
                    <button @click="show=false" class="ml-auto text-emerald-500 hover:text-emerald-800 p-1">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show"
                     class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 rounded-2xl text-sm font-semibold shadow-sm mb-2">
                    <div class="w-7 h-7 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <span>{{ session('error') }}</span>
                    <button @click="show=false" class="ml-auto text-rose-500 hover:text-rose-800 p-1">✕</button>
                </div>
            @endif
        </div>

        {{-- Main Page Scrollable Content --}}
        <main class="flex-1 overflow-y-auto px-4 lg:px-8 pb-10">
            @yield('content')
        </main>
    </div>

</div>

@stack('scripts')
</body>
</html>
