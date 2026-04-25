@props(['id' => '', 'src' => '', 'ext'=>'' ,'dif'=>'' , 'data'=>null])

@if(!empty($data))

@php
if(is_array($data->{$id}) && count($data->{$id}) == 1){
  $data->{$id} = $data->{$id}[0];
}
@endphp

@if(!is_array($data->{$id}))
<img class="image-{{ $id }}{{ $dif }}" src='{{ config('filesystems.disks.external.url', '').$src.$data->{$id}.$ext }}' {{ $attributes }}>
@else
<div class="owl-carousel owl-theme owl-single-dots">  
  @foreach($data->{$id} as $image)
    <img class="image-{{ $id }}{{ $dif }}" src='{{ config('filesystems.disks.external.url', '').$src.$image.$ext }}' {{ $attributes }}>
  @endforeach
</div>
@endif

@endif


@if(empty($data))
<div id='image-{{ $id }}{{ $dif }}'></div>
@push('fill')
    $('#image-{{ $id }}{{ $dif }}').empty();
    if(Array.isArray(data['{{ $id }}'])){
      if(data['{{ $id }}'].length == 1){
        data['{{ $id }}'] = data['{{ $id }}'][0];
      }
    }
    if(Array.isArray(data['{{ $id }}'])){
      var html_text = '<div class="owl-carousel owl-theme owl-single-dots">';
      for(var i = 0; i < data['{{ $id }}'].length; i++){
        var image_html_text = data['{{ $id }}'][i];
        var image_url = '{{ config('filesystems.disks.external.url', '').$src }}'+image_html_text+'{{ $ext }}';
        html_text += '<img class="image-{{ $id }}{{ $dif }}" src="'+image_url+'" {{ $attributes }}>';
      }
      html_text += '</div>';
      $('#image-{{ $id }}{{ $dif }}').html(html_text);
      $('.owl-single-dots').owlCarousel({
          loop:true,
          items: 1,
          nav:false,
          dots:true,
      });
    }
    else{
      $('#image-{{ $id }}{{ $dif }}').html('<img class="image-{{ $id }}{{ $dif }}" src="{{ config('filesystems.disks.external.url', '').$src }}'+data['{{ $id }}']+'{{ $ext }}" {{ $attributes }}>');
    }
@endpush
@endif
