<span id="text-{{ $id }}"></span>

@push('fill')
    $('#text-{{ $id }}').html(data['{{ $id }}']);
@endpush