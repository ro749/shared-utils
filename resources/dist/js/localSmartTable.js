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
            let column = { data: col };
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
            for(var data in table_data){
                var row = table_data[data];
                upload_table.push({product: row.id});
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