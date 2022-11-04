@props(['for'])

@error($for)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
