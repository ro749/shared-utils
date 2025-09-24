

<table id="{{ $table->get_id() }}" class="table bordered-table mb-0">
    <thead>
        <tr id="{{ $table->get_id() }}-header">
            @foreach($table->get_columns() as $key=>$column)
                <th>{{ $column->display }}</th>
            @endforeach
            @if($table->needsButtons())
                <th></th>
            @endif
        </tr>
    </thead>
    <tfoot>
        <tr id="{{ $table->get_id() }}-footer">
            @foreach($table->get_columns() as $key=>$column)
                <th>{{ $column->display }}</th>
            @endforeach
            @if($table->needsButtons())
                <th></th>
            @endif
        </tr>
    </tfoot>
</table>

@push('scripts')
<script>
    $('#{{ $table->get_id() }}').localSmartTable(@json($table->get_info()));
</script>
@endpush