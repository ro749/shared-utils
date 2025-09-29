@if(empty($unit) || (!empty($unit) && !empty($unit->{$id})))
<div id="if-{{ $id }}" {{ $attributes }}>
    {{ $slot }}
</div>
@endif

@if(empty($unit))
@push('fill')
    if(data['{{ $id }}'] && !(!isNaN(data['{{ $id }}']) && Number(data['{{ $id }}']) === 0)){
        $('#if-{{ $id }}').show();
    }
    else{
        $('#if-{{ $id }}').attr("style", "display: none !important;");
    }
@endpush
@endif