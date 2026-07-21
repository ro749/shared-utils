
<span class="text-{{ $id }}">
    @if(!empty($data))
        @if($data instanceof \Ro749\SharedUtils\Data\Data)
            @if(empty($en))
            {{ $data->get($id) }}
            @else
            {{ \Illuminate\Support\Facades\Session::get('lang','es') == 'es' ? $data->get($id) : $data->get($en) }}
            @endif
        @else
            {{ $data->{$id} }}
        @endif
    @endif
</span>

@if(empty($data))
@push('fill')
    @if(empty($en))
    $('.text-{{ $id }}').html(data['{{ $id }}']);
    @else
    $('.text-{{ $id }}').text(choosen_lang == 'es' ? data['{{ $id }}'] : data['{{ $en }}']);
    @endif
@endpush
@endif