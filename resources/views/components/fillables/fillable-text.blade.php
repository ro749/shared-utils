<span class="text-{{ $id }}">
    @if(!empty($unit))
        {{ $unit->{$id} }}
    @endif
</span>

@if(empty($unit))
@push('fill')
    $('.text-{{ $id }}').html(data['{{ $id }}']);
@endpush
@endif