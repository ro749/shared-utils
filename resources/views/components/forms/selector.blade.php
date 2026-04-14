@props(['selector'=>null, 'form_id'=> ''])

<select 
    id="{{ $name }}"
    name="{{ $name }}"
    x-model="form.{{ $name }}"
    style="width: 100%"
    {{ $attributes->class([
        'form-select',
        'w-auto',
        $selector->search ? 'select2' : '',
        $form_id != '' ? $form_id.'-field' : '',
        $class ?? '',
    ]) }}
    @if($selector->autosave)
    x-effect="submit()"
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
    $('#{{ $name }}').select2({
        theme: "bootstrap-5",
        dropdownAutoWidth: true,
        width: '{{ $selector->max_length == 0 ? 'auto' : ($selector->max_length <108 ? '108px' : $selector->max_length.'px') }}',
        allowClear: true,
        placeholder: '{{ $selector->placeholder }}', 
        
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
@push($push_reset)
    $('#{{ $name }}').val(null).trigger('change');
@endpush

@endif