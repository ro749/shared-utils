(function ($) {
    $.fn.addElementToTable = function (element) {
        return this.each(function () {
            const $table = $(this);
            var table = $table.DataTable();
            table.rows.add([element]);
            table.draw();
        });
    };
    $.fn.localSmartTable = function (options = {}) {
        const $table = $(this);
        var columns = [];
        for (let col in options.columns){
            var field = options.form.fields[col]??null;
            if(field==null){
                var column = { data: col };
            }
            else{
                
                let html_input = InputFactory.createInput(field);
                
                var column = {
                    data: null,
                    render: function (data, type, row) {
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
        });

        $table.on('click', '.delete-btn', function(event) {
            table.row($(this).parents('tr')).remove().draw();
        });
        $('#save-'+options.id).on('click', function() {
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

                upload_table.push(row);
            }
            $.ajax({
                url: '/table/'+options.id+'/save',
                method: 'POST',
                data: {data: upload_table},
                success: function(response) {
                    alert('Data saved successfully!');
                }
            })
        });
    };
})(jQuery);