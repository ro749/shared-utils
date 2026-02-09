<div id="{{ $id }}">
@if(!empty($data))
@foreach ($data->{$id} as $key => $item)
    @include($view, ['id'=>$id.$key, 'item' => $item])
@endforeach
@endif
</div>

<script>
@if(empty($data))
@push('fill')
    $('#{{ $id }}').empty();
    var {{ $id }}_html = '{!! File::get(resource_path('views/'.$view.'.blade.php')) !!}';
    for(var i = 0; i < data['{{ $id }}'].length; i++){
        var html_text = {{ $id }}_html.replace('@{{ $id }}', '{{ $id }}'+i).replace('@{{ $item }}', data['{{ $id }}'][i]);
        $('#{{ $id }}').append(html_text);
    }
@endpush
@endif
</script>