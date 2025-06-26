@foreach ($table->filters as $filter)
{!! $filter->render() !!}
@endforeach

<table id="{{ $table->id }}" class="table table-striped table-bordered">
    <thead>
        <tr>
            @foreach($table->columns as $key=>$column)
                <th>{{ $column->display }}</th>
            @endforeach
            @if($table->needsButtons())
                <th></th>
            @endif
        </tr>
    </thead>
    <tfoot>
        <tr>
            @foreach($table->columns as $key=>$column)
                <th>{{ $column->display }}</th>
            @endforeach
            @if($table->needsButtons())
                <th></th>
            @endif
        </tr>
    </tfoot>
</table>

@if($table->delete)
<x-sharedutils::modal id="delete-popup" onclose="closePopup('delete-popup');">
    <p id='delete-warning' style='text-align:center'>{{ $table->delete->warning }}</p>
    <div style='display:flex; justify-content:space-evenly'>
        <button class='btn btn-danger' onclick='closePopup("delete-popup")'>Cancelar</button>
        <button class='btn btn-success' id='on-delete'>Aceptar</button>
    </div>
</x-sharedutils::modal>
@endif

@push('scripts')
<script>
    $('#{{ $table->id }}').smartTable(@json($table));
</script>
@endpush