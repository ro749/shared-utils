@props(['element' => null, 'name' => ''])

<div id="{{ $name }}-container">
<input id="{{ $name }}" type="file" accept="{{ $element->accept ?? '*' }}" 
@if($element->autosave)
@change="storeFile($event); submit();"
@else
@change="showPreview($event);"
@endif
/>
<template x-if="errors['{{ $name }}']">
    <p class="form-error" x-text="errors['{{ $name }}']"></p>
</template>
@include('sharedutils::components.tables.smartTable', ['table' => $element->preview_table])
</div>
@push('scripts')
<script>
$('#{{ $element->push }}-cancel').on('click', function() {
    $.ajax({
        url: '{{ '/form/'.$element->push.'/cancel/'.$name }}',
        type: 'POST',
        success: function (data) {
            $('#{{ $element->preview_table->get_id() }}').DataTable().ajax.reload();
        }
    }); 

});
</script>
@endpush