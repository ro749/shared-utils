

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
</table>

@push('scripts')
<script>
    @php 
    $info = $table->get_info();
    $info['name'] = $name;
    @endphp
    $('#{{ $table->get_id() }}').localSmartTable(@json($info));
</script>
@endpush