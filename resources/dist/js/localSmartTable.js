(function ($) {
    $.fn.addElementToTable = function (element) {
        return this.each(function () {
            const $table = $(this);
            var counter = $table.data('counter');
            var id = $table.data('id');
            element.id = counter;
            
            var table = $table.DataTable();
            table.rows.add([element]);
            table.draw();
            $(document).trigger(id+'.added', [[counter,element]]);
            counter += 1;
            $table.data('counter',counter);
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
                let field_type = field.type;
                let html_input = InputFactory.createInput(field);
                var column = {
                    data: null,
                    render: function (data, type, row) {
                        let local_id = 'form.'+options.name+'['+data.id+'].'+col;
                        let error_id = options.name+'.'+data.id+'.'+col;
                        var div = $('<div></div>');
                        html_input.attr("id", options.name+'-'+row.id+'-'+col);
                        html_input.appendTo(div);
                        html_input.addClass('form-control');
                        if(field_type=='money'){
                            html_input.addClass('input-money');
                        }
                        else{
                            html_input.attr("x-model", local_id);
                        }
                        var error = $('<template x-if="errors[\''+error_id+'\']"><p class="form-error" x-text="errors[\''+error_id+'\']"></p></template>');
                        error.appendTo(div);
                        return div.prop('outerHTML');
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
            var id = $table.data('id');
            $(document).trigger(id+'.deleted', [table.row($(this).parents('tr')).data().id]);
            table.row($(this).parents('tr')).remove().draw();
            
        });
    };
})(jQuery);