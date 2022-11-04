@props(['placeHolderText'])
<select {!! $attributes->merge(['class' => 'form-control border-gray-200']) !!}>
    <dropdown-item :text="$placeHolderText"/>
    {{ $slot }}
</select>