<div id="{{ $id }}">
@if(!empty($data))
@foreach ($data->{$id} as $key => $item)
    {!! $item !!}
@endforeach
@endif
</div>

<script>
@if(empty($data))
@push('fill')
    $('#{{ $id }}').empty();
    for(var i = 0; i < data['{{ $id }}'].length; i++){
        var html_text = data['{{ $id }}'][i];
        $('#{{ $id }}').append(html_text);
    }
@endpush
@endif
</script>