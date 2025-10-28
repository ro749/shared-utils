@props(['selector'])
<select 
    id="{{ $name }}"
    name="{{ $name }}"
    x-model="form.{{ $name }}"
    style="width: 100%"
    {{ $attributes->class([
        'form-select',
        'w-auto',
        $selector->search ? 'select2' : '',
        $class ?? '',
    ]) }}
    @if($selector->autosave)
    @input.debounce.500ms="submit()"
    @endif
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

@if($selector->search)
@push($push_init)
    $('#{{ $name }}').select2({theme: "bootstrap-5",width: '100%'});
    $('#{{ $name }}').on('change', () => {
        this.form.{{ $name }} = $('#{{ $name }}').val();
    });
    @if(!empty($value))
    $('#{{ $name }}').val('{{ $value }}').trigger('change');
    @endif
@endpush
@push($push_reset)
    $('#{{ $name }}').val(null).trigger('change');
@endpush
@endif