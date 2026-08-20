@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
