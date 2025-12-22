@props(['label', 'value', 'color' => 'text-white', 'isBold' => false])

@php
    $valFloat = (float) $value;
    $displayValue = fmod($valFloat, 1) !== 0.00 
        ? number_format($valFloat, 2, ',', '.') 
        : number_format($valFloat, 0, ',', '.');
@endphp

<div class="bg-gray-800/40 p-5 rounded-xl border border-gray-700/50 text-center hover:bg-gray-800/80 transition-all duration-300 group relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

    <h3 class="text-2xl {{ $isBold ? 'font-extrabold' : 'font-bold' }} {{ $color }} tracking-wide relative z-10">
        {{ $displayValue }}<span class="text-sm font-normal text-gray-500 ml-0.5">%</span>
    </h3>
    <p class="text-[10px] uppercase font-bold text-gray-500 tracking-widest mt-2 relative z-10 group-hover:text-gray-300 transition-colors">{{ $label }}</p>
</div>