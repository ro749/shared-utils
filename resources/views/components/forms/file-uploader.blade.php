<div id="{{ $name }}-container">
<input id="{{ $name }}" type="file" accept="{{ $field->accept ?? '*' }}" 
@if($field->autosave)
@change="storeFile($event); submit();"
@else
@change="showPreview($event);"
@endif
/>
<template x-if="errors['{{ $name }}']">
    <p class="form-error" x-text="errors['{{ $name }}']"></p>
</template>
@include('sharedutils::components.tables.smartTable', ['table' => $field->preview_table])
</div>
@push('scripts')
<script>
//$('#{{ $form_id }}-button').hide();
$('#{{ $form_id }}-cancel').on('click', function() {
    $.ajax({
        url: '{{ '/form/'.$form_id.'/cancel/'.$name }}',
        type: 'POST',
        success: function (data) {
            $('#{{ $preview_table_id }}').DataTable().ajax.reload();
        }
    }); 

});
</script>
@endpush