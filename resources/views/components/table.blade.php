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
        <button class='btn btn-success' onclick='on_delete()'>Aceptar</button>
    </div>
</x-sharedutils::modal>
@endif

@push('scripts')
    <script>
        @if($table->delete)
        var delete_id = 0;
        @endif
        $(document).ready(function () {
            var table = $("#{{ $table->id }}").DataTable(
                {
                    "ajax": "/{{ $table->id }}/get",
                    "columns": [
                        @foreach($table->columns as $key=>$column)
                            { data: "{{ $key }}",
                            @switch($column->modifier)
                                @case(\Ro749\SharedUtils\Tables\ColumnModifier::METERS)
                                    render: function (data, type, row, meta) {
                                        return formatNumber(data) + "m²";
                                    },
                                    @break

                                @case(\Ro749\SharedUtils\Tables\ColumnModifier::FOOT)
                                    render: function (data, type, row, meta) {
                                        return formatNumber(data) + "sqft";
                                    },
                                    @break

                                @case(\Ro749\SharedUtils\Tables\ColumnModifier::MONEY)
                                    render: function (data, type, row, meta) {
                                        return "$" + formatNumber(data);
                                    },
                                    @break

                                @case(\Ro749\SharedUtils\Tables\ColumnModifier::DOLARS)
                                    render: function (data, type, row, meta) {
                                        return "$" + formatNumber(data);
                                    },
                                    @break

                                @case(\Ro749\SharedUtils\Tables\ColumnModifier::PERCENT)
                                    render: function (data, type, row, meta) {
                                        return formatNumber(data) + "%";
                                    },
                                    @break

                                @case(\Ro749\SharedUtils\Tables\ColumnModifier::DATE)
                                    render: function (data, type, row, meta) {
                                        return new Date(data).toLocaleDateString();
                                    },
                                    @break
                            @endswitch
                             },
                        @endforeach

                        @if($table->needsButtons())
                            { data: null,
                            render: function (data, type, row, meta) {
                                var buttons = "";
                                @if($table->view)
                                buttons += '<button type=\"button\" class=\"btn view-btn\" style=\"margin:auto\"><i class=\"fadeIn animated bx bx-show\" style=\"color: white;\"></i></button>';
                                @endif
                                @if($table->delete)
                                buttons += '<button type=\"button\" class=\"btn delete-btn\"><i class=\"fadeIn animated bx bx-trash\" style=\"color: white;\"></i></button>';
                                @endif
                                return buttons;
                            }
                            },
                        @endif
                    ]
                }
            );

            @if($table->view)
            $("#{{ $table->id }}").on('click', '.view-btn', function(event) {
                var table = $('#{{ $table->id }}').DataTable();
                window.location.href = '{{ $table->view->url }}?{{ $table->view->name }}=' + table.row($(this).parents('tr')).data().{{ $table->view->param }};}
            );
            @endif

            @if($table->delete)
            $("#{{ $table->id }}").on('click', '.delete-btn', function(event) {
                var table = $('#{{ $table->id }}').DataTable();
                var row = table.row($(this).parents('tr')).data();
                delete_id = row.id;
                var warning = "{{ $table->delete->warning }}";
                const matches = [...warning.matchAll(/\{(.*?)\}/g)];
                const args = matches.map(match => match[1].trim());
                for (const arg of args) {
                    warning = warning.replace('{' + arg + '}', row[arg]);
                }
                $('#delete-warning').text(warning);
                openPopup('delete-popup');
            });
            @endif
        });

        @if($table->delete)
        function on_delete(){
            var form = new FormData();
            form.append('id',delete_id);

            $.ajax({
                url: '/{{ $table->id }}/delete',
                type: 'POST',
                data: {
                    id: delete_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#{{ $table->id }}').DataTable().ajax.reload(null, false);
                    closePopup('delete-popup');
                },
            });
        }
        @endif
    </script>
@endpush