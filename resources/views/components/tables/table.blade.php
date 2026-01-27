<table id="{{ $table->get_id() }}" class="table bordered-table mb-0" style ="width:{{ 3500/36 }}% !important;">
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