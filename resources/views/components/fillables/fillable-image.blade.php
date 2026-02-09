@props(['id' => '', 'data' => '', 'src' => '', 'ext'=>'', 'unit'=>null])

@if(!empty($unit))

@php
if(is_array($unit->{$data}) && count($unit->{$data}) == 1){
  $unit->{$data} = $unit->{$data}[0];
}
@endphp

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
<div id='{{ $id }}'></div>
@push('fill')
    $('#{{ $id }}').empty();
    if(Array.isArray(data['{{ $data }}'])){
      if(data['{{ $data }}'].length == 1){
        data['{{ $data }}'] = data['{{ $data }}'][0];
      }
    }
    if(Array.isArray(data['{{ $data }}'])){
      var html_text = '<div class="owl-carousel owl-theme owl-single-dots">';
      for(var i = 0; i < data['{{ $data }}'].length; i++){
        var image_html_text = data['{{ $data }}'][i];
        var image_url = '{{ config('filesystems.disks.external.url', '').$src }}'+image_html_text+'{{ $ext }}';
        html_text += '<img class="image-{{ $id }}" src="'+image_url+'" {{ $attributes }}>';
      }
      html_text += '</div>';
      $('#{{ $id }}').html(html_text);
      $('.owl-single-dots').owlCarousel({
          loop:true,
          items: 1,
          nav:false,
          dots:true,
      });
    }
    else{
      $('#{{ $id }}').html('<img class="image-{{ $id }}" src="{{ config('filesystems.disks.external.url', '').$src }}'+data['{{ $data }}']+'{{ $ext }}" {{ $attributes }}>');
    }
@endpush
@endif
