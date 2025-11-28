<span class="text-{{ $id }}">
    @if(!empty($data))
        {{ $data->get($id) }}
    @endif
</span>

@if(empty($data))
@push('fill')
    $('.text-{{ $id }}').html(data['{{ $id }}']);
@endpush
@endif