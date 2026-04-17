@props(['element' => null, 'name' => ''])

<select 
    id="{{ $name }}"
    name="{{ $name }}"
    x-model="form.{{ $name }}"
    style="width: 100%"
    {{ $attributes->class([
        'form-select',
        'w-auto',
        $element->search ? 'select2' : '',
        $element->form_id != '' ? $element->form_id.'-field' : '',
        $class ?? '',
    ]) }}
    @if($element->autosave)
    x-effect="submit()"
    @endif
>
    <option disabled selected value>select</option>
    @foreach ($element->options as $k=>$v)
        @if($k==$element->value)
            <option value="{{ $k }}" selected>{{ $v }}</option> 
        @else
            <option value="{{ $k }}">{{ $v }}</option>
        @endif
    @endforeach
</select>

@if($element->search)
@push($element->form_id)
    $('#{{ $name }}').select2({
        theme: "bootstrap-5",
        dropdownAutoWidth: true,
        width: '{{ $element->max_length == 0 ? 'auto' : $element->max_length.'px' }}',
        allowClear: true,
        placeholder: '{{ $element->placeholder }}', 
        
    });
    $('#{{ $name }}').on('change', () => {
        this.form.{{ str_replace('-', '_', $name) }} = $('#{{ $name }}').val();
    });
    @if(!empty($value))
    $('#{{ $name }}').val('{{ $value }}').trigger('change');
    @endif
    @if(!empty($initial_data) && array_key_exists($name, $initial_data))
    $('#{{ $name }}').val('{{ $initial_data[$name] }}').trigger('change');
    @endif
@endpush
@push($element->form_id."_reset")
    $('#{{ $name }}').val(null).trigger('change');
@endpush

@endif