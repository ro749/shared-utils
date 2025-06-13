@props(['input'])
<input id="{{ $input->id }}" value="{{ $input->get() }}" {{ $attributes->class([
        'form-control',
        'w-auto',
        $input->autosave ? 'autosave' : '',
    ]) }}>
