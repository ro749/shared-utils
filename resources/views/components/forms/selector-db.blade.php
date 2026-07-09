@props(['element' => null, 'name' => '','form_id' => ''])
@if($element->selector_type == \Ro749\SharedUtils\Forms\SelectorType::Smart)
@php
$element->decide();
@endphp
@endif
@if($element->selector_type == \Ro749\SharedUtils\Forms\SelectorType::Static)
@php
$element->generate_options();
@endphp
@include('sharedutils::components.forms.selector', ['element' => $element, 'name' => $name, 'form_id' => $form_id])
@else
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
        'select2',
        $form_id != '' ? $form_id.'-field' : '',
        $class ?? '',
    ]) }}
    @if($element->autosave)
    @change="submit()"
    @endif
>
    <option disabled selected value>select</option>
</select>
@push($form_id)
    $('#{{ $name }}').select2({
        theme: "bootstrap-5",
        width: '100% !important',
        allowClear: true,
        placeholder: '{{ $element->placeholder }}', 
        @if(!empty($element->accept_new_values))
        tags: true,
        @endif
        ajax: {
            url: '/form/{{ $form_id }}/search/{{ $name }}',
            delay: 250,
            processResults: function (data) {
                var ans = {
                  results: Object.entries(data).map(([id, text]) => ({ 'id': id, text }))
                };
                return ans;
            }
        }
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
@push($form_id."_reset")
    $('#{{ $name }}').val(null).trigger('change');
@endpush
@endif