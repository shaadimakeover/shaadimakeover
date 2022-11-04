@props(['color'])
<a {{ $attributes->merge(['href' => '#', 'class' => 'btn btn-'.$color]) }}>
    Cancel
</a>