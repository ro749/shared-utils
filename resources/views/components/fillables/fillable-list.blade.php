<div class="{{ $id }}">
@if(!empty($data))
@foreach ($data->{$id} as $key => $item)
    @if(empty($en))
    {!! $item !!}
    @else
    {!! \Illuminate\Support\Facades\Session::get('lang','es') == 'es' ? $item : $data->{$en}[$key] !!}
    @endif
@endforeach
@endif
</div>

<script>
@if(empty($data))
@once($id)
@push('fill')
    $('.{{ $id }}').empty();
    for(var i = 0; i < data['{{ $id }}'].length; i++){
        @if(empty($en))
        var html_text = data['{{ $id }}'][i];
        @else
        var html_text = choosen_lang == 'es' ? data['{{ $id }}'][i] : data['{{ $en }}'][i];
        @endif
        $('.{{ $id }}').append(html_text);
    }
@endpush
@endonce
@endif
</script>