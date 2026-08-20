<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold px-5 py-3 rounded-2xl transition-all text-xs shadow-md shadow-rose-900/20 cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500']) }}>
    {{ $slot }}
</button>
