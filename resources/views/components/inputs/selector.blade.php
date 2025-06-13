@props(['selector'])
<select 
    {{ $attributes->class([
        'form-select',
        'w-auto',
        $selector->search ? 'select2' : '',
    ]) }}>
    @foreach ($selector->options as $k=>$v)
        @if($k==$selector->value)
            <option value="{{ $k }}" selected>{{ $v }}</option> 
        @else
            <option value="{{ $k }}">{{ $v }}</option>
        @endif
    @endforeach
</select>