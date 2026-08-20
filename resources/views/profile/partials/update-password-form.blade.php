<section>
    <header class="border-b border-slate-100 pb-4">
        <h2 class="text-base font-extrabold text-slate-900">
            Pembaruan Kata Sandi
        </h2>

        <p class="mt-1 text-xs text-slate-500 font-medium">
            Pastikan akun Anda menggunakan kata sandi yang panjang dan aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div class="space-y-1.5">
            <x-input-label for="update_password_current_password" value="Kata Sandi Saat Ini" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1 text-rose-500 text-xs font-semibold" />
        </div>

        <div class="space-y-1.5">
            <x-input-label for="update_password_password" value="Kata Sandi Baru" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1 text-rose-500 text-xs font-semibold" />
        </div>

        <div class="space-y-1.5">
            <x-input-label for="update_password_password_confirmation" value="Konfirmasi Kata Sandi Baru" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1 text-rose-500 text-xs font-semibold" />
        </div>

        <div class="flex items-center gap-4 pt-3 border-t border-slate-100">
            <x-primary-button>Perbarui Kata Sandi</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs font-bold text-emerald-600"
                >Kata sandi berhasil diperbarui.</p>
            @endif
        </div>
    </form>
</section>
