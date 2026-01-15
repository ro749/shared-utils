@php
    // Parse the data-style template
    $properties = array_filter(array_map('trim', explode(';', $styleData)));
    $parsedStyles = [];
    
    foreach ($properties as $property) {
        [$cssProperty, $valueTemplate] = array_map('trim', explode(':', $property, 2));
        $parsedStyles[$cssProperty] = $valueTemplate;
    }
@endphp
<div {{ $attributes->except(['data', 'style-data']) }}
@if(!empty($data))
style="
    @foreach ($parsedStyles as $cssProperty => $valueTemplate)
        {{ $cssProperty }}: {{ $data->get($valueTemplate) }};
    @endforeach
"
@endif
>
    {{ $slot }}
</div>
<script>
@if(empty($data))
@push('fill')
    $('#{{ $id }}').css({
        @foreach ($parsedStyles as $cssProperty => $valueTemplate)
            '{{ $cssProperty }}': data['{{ $valueTemplate }}'],
        @endforeach
    });
@endpush
@endif
</script>