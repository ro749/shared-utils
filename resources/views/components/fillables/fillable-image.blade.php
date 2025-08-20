@props(['id' => '', 'src' => '', 'ext'=>''])
<img id="image-{{ $id }}">

<script>
    @push('fill')
        $('#image-{{ $id }}').attr('src', '{{ $src }}'+data['{{ $id }}']+'{{ $ext }}');
    @endpush
</script>