<table id="{{ $table->get_id() }}" class="table bordered-table mb-0" style ="width:{{ 550/6 }}% !important;">
    <thead>
        <tr id="{{ $table->get_id() }}-header">
            @foreach($table->get_columns() as $key=>$column)
                <th>{{ $column->display }}</th>
            @endforeach
            @if($table->needsButtons())
                <th>{{ $table->buttons_label }}</th>
            @endif
        </tr>
    </thead>
    
</table>

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