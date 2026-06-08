@props(['element' => null, 'name' => '','form_id' => ''])

<select 
    id="{{ $name }}"
    name="{{ $name }}"
    x-model="form.{{ $name }}"
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
        dropdownAutoWidth: true,
        width: '{{ $element->max_length == 0 ? 'auto' : ($element->max_length <108 ? '108px' : $element->max_length.'px') }}',
        allowClear: true,
        placeholder: '{{ $element->placeholder }}', 
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