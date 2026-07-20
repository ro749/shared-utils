@props(['element' => null, 'name' => '','form_id' => ''])

<select 
    id="{{ $name }}"
    name="{{ $name }}"
    @if($element->register_in_form)
    x-input="form.{{ $name }}"
    @endif
    style="width: 100%"
    {{ $attributes->class([
        'form-select',
        'w-auto',
        $element->search ? 'select2' : '',
        $element->form_id != '' ? $element->form_id.'-field' : '',
        $class ?? '',
    ]) }}
    @if($element->autosave)
    @change="submit()"
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
@push($form_id)
    $('#{{ $name }}').select2({
        theme: "bootstrap-5",
        dropdownAutoWidth: true,
        width: '{{ $element->max_length == 0 ? 'auto' : ($element->max_length <108 ? '108px' : $element->max_length.'px') }}',
        allowClear: true,
        placeholder: '{{ $element->placeholder }}', 
        @if(!empty($element->accept_new_values))
        tags: true,
        @endif
    });
@endpush

@endif