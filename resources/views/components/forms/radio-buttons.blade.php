@props(['selector'])
<div id="{{ $name }}" name="{{ $name }}" {{ $attributes }}>
    @foreach ($selector->options as $k=>$v)
        @include($selector->button_view, ['value' => $k, 'display' => $v])
    @endforeach
</div>