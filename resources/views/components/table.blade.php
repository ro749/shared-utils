@foreach ($table->filter as $filter)
    {{ $filter->render() }}

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
                                @case(\Ro749\SharedUtils\Tables\ColumnModifier::ENUM)
                                render: function (data, type, row, meta) {
                                        return {{ $key }}[data];
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
                                @if($table->is_editable)
                                buttons += '<button type=\"button\" class=\"btn edit-btn\" style=\"margin:auto\"><i class=\"fadeIn animated bx bx-edit-alt\" style=\"color: white; margin:auto\"></i></button>';
                                @endif
                                @if($table->delete)
                                buttons += '<button type=\"button\" class=\"btn delete-btn\"><i class=\"fadeIn animated bx bx-trash\" style=\"color: white;\"></i></button>';
                                @endif
                                @if($table->is_editable)
                                buttons = '<div class=\"normalbuttons\" style=\"display:flex; flex-direction:row\">' + buttons + '</div><div style=\"display:none\"> <button type=\"button\" class=\"btn save-btn\"><i class=\"fadeIn animated bx bx-save\" style=\"color: white; margin:auto\"></i></button> <button type=\"button\" class=\"btn cancel-btn\" ><i class=\"fadeIn animated bx bx-x-circle\" style=\"color: white; \"></i></button> </div>';
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

            @if($table->is_editable)
            $("#{{ $table->id }}").on('click', '.edit-btn', function(event) {
                this.parentElement.style.display = 'none';
                this.parentElement.parentElement.children[1].style.display = 'flex';
                this.parentElement.parentElement.children[1].style.justifyContent = 'left';
                var table = $("#{{ $table->id }}").DataTable();
                var row = table.row($(this).parents('tr'));
            $col_num = 0;
                @foreach($table->columns as $key=>$column)
                    @if($column->editable)
                    
                    @endif
                @endforeach
            foreach($input["columns"] as $key=>$column){
                if(isset($column["editable"])){
                    if(isset($column["options"])){
                        $ans .= "table_editor({row:row,col:".$col_num.",create:create_selector,options:".json_encode($column["options"])."});";
                    }
                    else if(isset($column["column"])){
                        $ans .= "table_editor({row:row,col:".$col_num.",create:create_selector,options:".$key."s,values:".$key."s_values,db:'true'});";
                    }
                    else if(isset($column["modifier"]) && $column["modifier"] == ColumnModifier::Date){
                        $ans .= "table_editor({row:row,col:".$col_num.",create:create_date_editor,date:true});";
                    }
                    else{
                        $ans .= "table_editor({row:row,col:".$col_num.",create:".((isset($column["modifier"])) ? "create_input_number" : "create_input_text")."});";
                    }
                
                }
                $col_num++;
            }    
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