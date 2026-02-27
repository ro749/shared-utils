@props(['images'])
<div class="owl-carousel owl-theme owl-single-dots">  
  @foreach($images as $image)
    <img src='{{ $image }}' {{ $attributes }}>
  @endforeach
</div>