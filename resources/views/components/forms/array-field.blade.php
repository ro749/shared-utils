@props(['element' => null, 'name' => '','form_id' => ''])
@php
    $element->table->id = $name.'-table';
    $element->selector->register_in_form = false;
@endphp
<div style="display: flex; flex-direction: row; align-items: center;">
    @include('sharedutils::components.forms.selector-db',[
        'element'=>$element->selector,
        'name'=>$name.'-selector',
        'form_id'=>$form_id,
    ])
    @include('sharedutils::components.tables.localSmartTable',[
        'table'=>$element->table,
    ])
</div>

@once
    @push('script-includes')
        <script src="{{ asset('vendor/shared-utils/js/arrayField.js') }}"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        $(document).ready(function(){
            $(document).arrayField('{{ $name }}');
        });
    </script>
@endpush
<script>
@push($form_id)
    this.form.{{ $name }} = {};
    $(document).on('{{ $element->table->id }}.added', (e, data) => {
        this.form.{{ $name }}[data[0]] = data[1];
    });
    $(document).on('{{ $element->table->id }}.deleted', (e, data) => {
        delete this.form.{{ $name }}[data];
    });
@endpush
</script>