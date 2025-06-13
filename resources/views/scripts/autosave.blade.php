
@once("autosave-{{ $id }}")
    @push('js')
        $('#{{ $id }}').autoSaveInput({url: {{ $url }}});
    @endpush
@endonce