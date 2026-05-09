@props([
    'name',
    'label',
    'type'        => 'text',
    'placeholder' => '',
    'value'       => null,
    'autofocus'   => false,
    'required'    => false,
])

@php
    $temErro     = $errors->has($name);
    // Por segurança, password nunca é repopulado.
    $valorInput  = $type === 'password' ? '' : ($value ?? old($name));
@endphp

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-zinc-300 mb-1.5">
        {{ $label }}
    </label>

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ $valorInput }}"
        placeholder="{{ $placeholder }}"
        @class([
            'w-full bg-[#1c1c1e] text-white placeholder-zinc-500 text-sm',
            'rounded-lg px-3.5 py-2.5 outline-none transition-colors',
            'border focus:ring-1',
            'border-zinc-700 focus:border-blue-500 focus:ring-blue-500/40' => !$temErro,
            'border-red-500/70 focus:border-red-500 focus:ring-red-500/40' => $temErro,
        ])
        @if ($autofocus) autofocus @endif
        @if ($required) required @endif
    >

    @error($name)
        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
    @enderror
</div>
