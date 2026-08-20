<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-slate-400']) }}>
    {{ $slot }}
</button>
