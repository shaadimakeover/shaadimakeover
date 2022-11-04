@props(['value','required'=>false])

<label class="{{ $required ? 'label-required' : ''  }}">
    {{ $value ?? $slot }}
</label>