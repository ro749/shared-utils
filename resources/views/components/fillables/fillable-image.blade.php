@props(['id' => '', 'data' => '', 'src' => '', 'ext'=>'', 'unit'=>null])

@php
if(is_array($unit->{$data}) && count($unit->{$data}) == 1){
  $unit->{$data} = $unit->{$data}[0];
}
@endphp

@if(!empty($unit))
@if(is_string($unit->{$data}))
<img class="image-{{ $id }}" src='{{ config('filesystems.disks.external.url', '').$src.$unit->{$data}.$ext }}' {{ $attributes }}>
@else
<div class="owl-carousel owl-theme owl-single-dots">  
  @foreach($unit->{$data} as $image)
    <img class="image-{{ $id }}" src='{{ config('filesystems.disks.external.url', '').$src.$image.$ext }}' {{ $attributes }}>
  @endforeach
</div>
@endif

@endif

@if(empty($unit))
@push('fill')
    $('.image-{{ $id }}').attr('src', '{{ config('filesystems.disks.external.url', '').$src }}'+data['{{ $data }}']+'{{ $ext }}');
@endpush
@endif