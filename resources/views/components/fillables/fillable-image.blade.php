@props(['id' => '', 'data' => '', 'src' => '', 'ext'=>'', 'unit'=>null])
<img class="image-{{ $id }}"  
@if(!empty($unit))
  src='{{ config('filesystems.disks.external.url', '').$src.$unit->{$data}.$ext }}'
@endif
  {{ $attributes }}
>

@if(empty($unit))
@push('fill')
    $('.image-{{ $id }}').attr('src', '{{ config('filesystems.disks.external.url', '').$src }}'+data['{{ $data }}']+'{{ $ext }}');
@endpush
@endif