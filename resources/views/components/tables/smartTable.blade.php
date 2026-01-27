@include('sharedutils::components.tables.table', ['table' => $table])

@if($table->delete)
<x-sharedutils::modal id="delete-popup" onclose="closePopup('delete-popup');">
    <p id='delete-warning' style='text-align:center'></p>
    <div style='display:flex; justify-content:space-evenly'>
        <button class='btn btn-warning-600' onclick='closePopup("delete-popup")'>Cancelar</button>
        <button class='btn btn-danger-600' id='on-delete'>Borrar</button>
    </div>
</x-sharedutils::modal>
@endif

@foreach($table->buttons as $button)
    @if(!empty($button->warning_popup))
        @include($button->warning_popup, ['class' => $button->button_class])
    @endif
    @if(!empty($button->success_popup))
        @include($button->success_popup, ['class' => $button->button_class])
    @endif
@endforeach

@push('scripts')
<script>
    $('#{{ $table->get_id() }}').smartTable(@json($table->get_info()));
</script>
@endpush
<script>
@push('reset')
$('#{{ $table->get_id() }}').DataTable().destroy();
$.ajax({
    url: '/table/{{ $table->get_id() }}/metadata',
    success: function(data) {
        $('#{{ $table->get_id() }}').html(data['view']);
        $('#{{ $table->get_id() }}').smartTable(data);
    }
})
//$('#{{ $table->get_id() }}').DataTable().ajax.reload();
@endpush
</script>