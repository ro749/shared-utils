@props(['selector'])
<select 
    id="{{ $name }}"
    name="{{ $name }}"
    x-model="form.{{ $name }}"
    {{ $attributes->class([
        'form-select',
        'w-auto',
        $selector->search ? 'select2' : '',
        $class ?? '',
    ]) }}
>
    <option disabled selected value>select</option>
    @foreach ($selector->options as $k=>$v)
        @if($k==$selector->value)
            <option value="{{ $k }}" selected>{{ $v }}</option> 
        @else
            <option value="{{ $k }}">{{ $v }}</option>
        @endif
    @endforeach
</select>