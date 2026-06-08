@props(['element' => null, 'name' => '', 'form_id' => ''])

<div id="{{ $name }}-container">
    <div id="{{ $name }}-input" @drop.prevent="showPreview($event.dataTransfer);">
        <p>Subir Archivo:</p>
        <label for="{{ $name }}" style="
        background-image: url('https://propstudios.mx/img/upload.png'); 
        width: 216px; 
        height: 108px;
        background-repeat: no-repeat;
        background-position: center;
        border: 2px dashed #147dfe;
        "></label>
        <input id="{{ $name }}" name="{{ $name }}" type="file" style="display: none" accept="{{ $element->accept ?? '*' }}" 
        @if($element->autosave)
        @change="storeFile($event); submit();"
        @else
        @change="showPreview($event);"
        @endif
        />
        <template x-if="errors['{{ $name }}']">
            <p class="form-error" x-text="errors['{{ $name }}']"></p>
        </template>
    </div>
    <div id="{{ $name }}-preview">
    @include('sharedutils::components.tables.smartTable', ['table' => $element->preview_table])
    </div>
</div>
@push('scripts')
<script>
$('#{{ $form_id }}-cancel').on('click', function() {
    $.ajax({
        url: '{{ '/form/'.$form_id.'/cancel/'.$name }}',
        type: 'POST',
        success: function (data) {
            $('#{{ $element->preview_table->get_id() }}').DataTable().ajax.reload();
        }
    }); 
});
$('#{{ $element->preview_table->get_id() }}').on('loaded', function(e,data) {
    if(data){
        $('#{{ $name }}-input').hide();
        $('#{{ $name }}-preview').show();
        $('#{{ $form_id }}-button').show();
    }
    else{
        $('#{{ $name }}-input').show();
        $('#{{ $name }}-preview').hide();
        $('#{{ $form_id }}-button').hide();
    }
});
</script>
@endpush