<x-guest-layout>
    <div class="relative min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-10 bg-candidate overflow-hidden">
        <!-- Overlay Gradient for Readability & Ambience -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/95 via-red-950/85 to-slate-950/95 backdrop-blur-[3px]"></div>
        
        <!-- Ambient Decorative Glow Spheres -->
        <div class="absolute top-1/4 -left-20 w-80 h-80 bg-red-600/30 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-amber-500/20 rounded-full blur-[130px] pointer-events-none"></div>

        <!-- Main Content Grid -->
        <div class="relative z-10 w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center py-6">
            
            <!-- Left Column: Candidate & Campaign Showcase -->
            <div class="lg:col-span-7 flex flex-col justify-center text-white space-y-6">
                <!-- Top Badge -->
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white/10 border border-white/20 backdrop-blur-md self-start shadow-sm">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                    </span>
                    <span class="text-xs font-extrabold tracking-wider uppercase text-red-200">Sistem Real Count Pilur Sabdodadi</span>
                </div>

                <!-- Candidate Hero Title & Subtitle -->
                <div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight text-white drop-shadow-md">
                        Nurma Setiawan, <span class="text-red-400">SE</span>
                    </h1>
                    <p class="mt-2 text-base sm:text-lg text-amber-300 font-bold tracking-wide flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Calon Lurah Kalurahan Sabdodadi
                    </p>
                </div>

                <!-- Slogan Highlight Box -->
                <div class="relative p-6 rounded-2xl bg-gradient-to-r from-red-950/70 to-slate-900/70 border border-red-500/30 backdrop-blur-md overflow-hidden shadow-2xl">
                    <div class="absolute -right-4 -bottom-4 text-white/5 font-black text-8xl pointer-events-none select-none">#1</div>
                    <p class="text-lg sm:text-xl font-extrabold italic text-slate-100 leading-snug">
                        "Bekerja Adil dan Berseri Bersama Memajukan Desa Sabdodadi"
                    </p>
                    <p class="mt-2 text-sm font-bold text-red-400 tracking-wider uppercase">
                        Kita Pasti Bisa!
                    </p>
                </div>

                <!-- 5 Pillars Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-1">
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-700/50 backdrop-blur-sm flex items-center gap-2.5">
                        <span class="p-2 rounded-lg bg-red-500/20 text-red-400 font-bold text-sm">⚖️</span>
                        <div>
                            <p class="text-xs font-bold text-white">Adil</p>
                            <p class="text-[10px] text-slate-400">Berperilaku Setara</p>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-700/50 backdrop-blur-sm flex items-center gap-2.5">
                        <span class="p-2 rounded-lg bg-amber-500/20 text-amber-400 font-bold text-sm">🤝</span>
                        <div>
                            <p class="text-xs font-bold text-white">Bermartabat</p>
                            <p class="text-[10px] text-slate-400">Melayani Dengan Pasti</p>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-700/50 backdrop-blur-sm flex items-center gap-2.5">
                        <span class="p-2 rounded-lg bg-emerald-500/20 text-emerald-400 font-bold text-sm">💰</span>
                        <div>
                            <p class="text-xs font-bold text-white">Sejahtera</p>
                            <p class="text-[10px] text-slate-400">Membangun Ekonomi</p>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-700/50 backdrop-blur-sm flex items-center gap-2.5">
                        <span class="p-2 rounded-lg bg-sky-500/20 text-sky-400 font-bold text-sm">🙏</span>
                        <div>
                            <p class="text-xs font-bold text-white">Religius</p>
                            <p class="text-[10px] text-slate-400">Bersih Pungli</p>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-700/50 backdrop-blur-sm flex items-center gap-2.5 sm:col-span-2 lg:col-span-1">
                        <span class="p-2 rounded-lg bg-purple-500/20 text-purple-400 font-bold text-sm">🚀</span>
                        <div>
                            <p class="text-xs font-bold text-white">Inovatif</p>
                            <p class="text-[10px] text-slate-400">Potensi Desa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Glassmorphism Login Form -->
            <div class="lg:col-span-5 w-full">
                <div class="relative bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-white/50 dark:border-slate-800 rounded-3xl p-6 sm:p-8 lg:p-9 shadow-2xl shadow-slate-950/60">
                    
                    <!-- Candidate Thumbnail & Card Header -->
                    <div class="flex items-center gap-4 mb-6 pb-5 border-b border-slate-200/80 dark:border-slate-800">
                        <div class="relative w-14 h-14 rounded-2xl overflow-hidden ring-2 ring-red-600 ring-offset-2 ring-offset-white dark:ring-offset-slate-900 flex-shrink-0 shadow-md">
                            <img src="{{ asset('images/kandidat.jpg') }}" alt="Nurma Setiawan, SE" class="w-full h-full object-cover object-top">
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Masuk Akun</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Hitung Suara Tim Pemenangan</p>
                        </div>
                    </div>

                    <!-- Session Status Notification -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPassword: false }">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Email / Username
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       autocomplete="username"
                                       placeholder="nama@email.com"
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-semibold text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent focus:bg-white dark:focus:bg-slate-800 transition-all shadow-sm">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        <!-- Password -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="password" class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Kata Sandi
                                </label>
                                @if (Route::has('password.request'))
                                    <a class="text-xs font-bold text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors" href="{{ route('password.request') }}">
                                        Lupa kata sandi?
                                    </a>
                                @endif
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input id="password" 
                                       :type="showPassword ? 'text' : 'password'" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-semibold text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent focus:bg-white dark:focus:bg-slate-800 transition-all shadow-sm">
                                
                                <button type="button" 
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors focus:outline-none"
                                        tabindex="-1">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.97 8.97 0 013.122-.603c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.007 3.518M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between pt-1">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input id="remember_me" 
                                       type="checkbox" 
                                       name="remember"
                                       class="w-4 h-4 rounded border-slate-300 text-red-600 shadow-sm focus:ring-red-500 focus:ring-offset-0 cursor-pointer">
                                <span class="ms-2.5 text-xs font-semibold text-slate-600 dark:text-slate-300">Ingat Sesi Saya</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 via-red-700 to-rose-800 hover:from-red-700 hover:to-rose-900 text-white font-extrabold px-6 py-3.5 rounded-2xl transition-all text-sm shadow-xl shadow-red-600/30 hover:shadow-red-600/50 transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer">
                                <svg class="w-5 h-5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                <span>Masuk ke Sistem</span>
                            </button>
                        </div>
                    </form>

                    <!-- Footer Security Note -->
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80 text-center">
                        <p class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Sistem Real Count & Quick Count Terproteksi
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
