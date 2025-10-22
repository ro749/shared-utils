(function ($) {
    $.fn.addElementToTable = function (element) {
        return this.each(function () {
            const $table = $(this);
            var counter = $table.data('counter');
            var id = $table.data('id');
            counter += 1;
            $table.data('counter',counter);
            element.id = counter;
            var table = $table.DataTable();
            table.rows.add([element]);
            table.draw();
            $(document).trigger(id+'.added', [element]);
        });
    };
    $.fn.localSmartTable = function (options = {}) {
        const $table = $(this);
        $table.data('counter',0);
        $table.data('id',options.id);
        var columns = [];
        
        for (let col in options.columns){
            var field = options.form.fields[col]??null;
            var column = options.columns[col];
            if(field==null){
                if(column.fillable){
                    var column = {
                        data: null,
                        render: function (data, type, row) {
                            return '<span class="'+col+'" id="'+col+'-'+row.id+'"></span>';
                        }
                    }
                }
                else{
                    var column = { data: col };
                }
            }
            else{
                field.class = 'input-'+col;
                
                let html_input = InputFactory.createInput(field);
                
                var column = {
                    data: null,
                    render: function (data, type, row) {
                        html_input.attr("id", col+'-'+row.id);
                        return html_input.prop('outerHTML');
                    }
                }
            }
            columns.push(column);
        }
        columns.push({
            data: null,
            render: function (data, type, row) {
                var buttons = '';
                for(var button in options.buttons){
                    buttons += `
                        <button type="button" class="btn `+options.buttons[button].button_class+` w-32-px h-32-px `+options.buttons[button].background_color_class+' '+options.buttons[button].text_color_class+` rounded-circle d-inline-flex align-items-center justify-content-center">
                            <iconify-icon icon="`+options.buttons[button].icon+`"> 
                            </iconify-icon>
                        </button>
                    `;
                }
                return buttons;
            }
        });
        
        var table = $table.DataTable({
            columns: columns,
            data: [],
            searching: false,
            paging: false
        });

        $table.on('click', '.delete-btn', function(event) {
            table.row($(this).parents('tr')).remove().draw();
        });
        $(document).on('submit-'+options.parent_form.id, function(event,parent_data) {
            var table_data = table.data().toArray();
            var upload_table = [];
            var rowIndex = 0;
            var columns = Object.keys(options.columns);
            for(var data in table_data){
                var row = {};
                for(var field_key in options.form.fields){
                    var field = options.form.fields[field_key];
                    if(field.type=='hidden'){
                        row[field_key] = table_data[data][field_key];
                    }
                    else{
                        row[field_key] = table.row(rowIndex).node().children[columns.indexOf(field_key)].children[0].value;
                    }
                    
                }
                rowIndex++;
                upload_table.push(row);
            }
            $.ajax({
                url: '/table/'+options.id+'/save',
                method: 'POST',
                data: {
                    parent_data: parent_data,
                    table_data: upload_table
                },
                success: function(response) {
                    alert('Data saved successfully!');
                }
            })
        });
    };
})(jQuery);