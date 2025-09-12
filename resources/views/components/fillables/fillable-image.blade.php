@props(['id' => '', 'data' => '', 'src' => '', 'ext'=>'', 'unit'=>null])
<img class="image-{{ $id }}"  
@if(!empty($unit))
  src='{{ $src }}{{ $unit->{$data} }}{{ $ext }}'
@endif
  {{ $attributes }}
>

@if(empty($unit))
@push('fill')
    $('.image-{{ $id }}').attr('src', '{{ $src }}'+data['{{ $data }}']+'{{ $ext }}');
@endpush
@endif