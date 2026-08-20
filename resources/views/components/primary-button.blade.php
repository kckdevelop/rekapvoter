<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-extrabold px-6 py-3 rounded-2xl transition-all text-xs shadow-md shadow-emerald-900/20 cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500']) }}>
    {{ $slot }}
</button>
