@props(['element' => null, 'name' => ''])

<div id="{{ $name }}" name="{{ $name }}" {{ $attributes }}>
    @foreach ($element->options as $k=>$v)
        @include($element->button_view, ['value' => $k, 'display' => $v])
    @endforeach
</div>