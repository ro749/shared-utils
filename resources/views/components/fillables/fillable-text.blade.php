
<span class="text-{{ $id }}">
    @if(!empty($data))
        @if($data instanceof \Ro749\SharedUtils\Data\Data)
            {{ $data->get($id) }}
        @else
            {{ $data->{$id} }}
        @endif
    @endif
</span>

@if(empty($data))
@push('fill')
    $('.text-{{ $id }}').html(data['{{ $id }}']);
@endpush
@endif