@php
    $message = $errors->first($name);
@endphp

<p
    class="mt-2 text-sm font-medium text-rose-600 {{ $message ? '' : 'hidden' }}"
    data-field-error="{{ $name }}"
>
    {{ $message }}
</p>
