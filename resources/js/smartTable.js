(function ($) {
    $.fn.smartTable = function (options = {}) {
        return this.each(function () {
            const $table = $(this);
            var selectors = [];
            const columns = [];
            for (let col in options.columns){
                let column = { data: col };
                switch (options.columns[col].modifier) {
                    case 'money':
                        column.render = (data) => `$${Number(data).toLocaleString('es-MX')}`;
                        break;
                    case 'percent':
                        column.render = (data) => `${data}%`;
                        break;
                    case 'date':
                        column.render = (data) => new Date(data).toLocaleDateString();
                        break;
                    case 'enum':
                        column.render = (data) => window[col][data];
                        break;
                    // add more modifiers as needed
                }
                if(options.columns[col].table && options.columns[col].column && options.columns[col].editable){
                    column.render = (data) => data==0?"":selectors[col][data];
                }
                columns.push(column);
            }
            if (options.needs_buttons) {
                columns.push({
                    data: null,
                    render: function (data, type, row, meta) {
                        let buttons = '';

                        if (options.view) {
                            buttons += '<button type="button" class="btn view-btn"><i class="bx bx-show"></i></button>';
                        }

                        if (options.is_editable) {
                            buttons += '<button type="button" class="btn edit-btn"><i class="bx bx-edit-alt"></i></button>';
                        }

                        if (options.delete) {
                            buttons += '<button type="button" class="btn delete-btn"><i class="bx bx-trash"></i></button>';
                        }

                        if (options.is_editable) {
                            buttons = `
                                <div class="normalbuttons" style="display:flex">`+buttons+`</div>
                                <div style="display:none">
                                    <button type="button" class="btn save-btn"><i class="bx bx-save"></i></button>
                                    <button type="button" class="btn cancel-btn"><i class="bx bx-x-circle"></i></button>
                                </div>
                            `;
                        }

                        return buttons;
                    }
                });
            }

            var table = $table.DataTable({
                ajax: "/table/"+options.id+"/get",
                columns: columns,
                serverSide: true
            });
            
            
            
            if(options.view){
                $table.on('click', '.view-btn', function(event) {
                    window.location.href = options.view.url+'?'+options.view.name+'='+table.row($(this).parents('tr')).data()[options.view.param];
                    
                });
            }
            
            if(options.delete){
                $table.on('click', '.delete-btn', function(event) {
                    var row = table.row($(this).parents('tr')).data();
                    //$table.data('delete_id', row.id);
                    var warning = options.delete.warning;
                    const matches = [...warning.matchAll(/\{(.*?)\}/g)];
                    const args = matches.map(match => match[1].trim());
                    for (const arg of args) {
                        warning = warning.replace('{' + arg + '}', row[arg]);
                    }
                    $('#delete-warning').text(warning);
                    $('#on-delete')[0].onclick = function(){
                        $.ajax({
                            url: '/table/'+options.id+'/delete',
                            type: 'POST',
                            data: {
                                id: row.id,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                table.ajax.reload(null, false);
                                closePopup('delete-popup');
                            },
                        });
                    };
                    openPopup('delete-popup');
                });
                
                table.delete_id = 0;
                
                table.on_delete = function(){
                    
                }
            }
            
            if(options.is_editable){
                $table.on('click', '.edit-btn', function(event) {
                    this.parentElement.style.display = 'none';
                    this.parentElement.parentElement.children[1].style.display = 'flex';
                    this.parentElement.parentElement.children[1].style.justifyContent = 'left';
                    var row = table.row($(this).parents('tr'));
                    var colnum = 0;
                    var has_date = false;
                    var has_selector = false;
                    for(var key in options.columns){
                        var col = options.columns[key];
                        if(col.editable){
                            var cell = row.node().getElementsByTagName('td')[colnum];
                            var hidden = "<span style='display:none' class='edit-cancel-recover'>"+cell.innerHTML+"</span>";
                            if(col.table && col.column){
                                hidden = hidden+'<select class="form-select db-select" id="'+key+'"><option disabled selected value>selec</option>';
                                for(var option in selectors[key]){
                                    hidden += row.data()[key] == option ?
                                        '<option value=' + option + ' selected>' + selectors[key][option] + '</option>' :
                                        '<option value=' + option + '>' + selectors[key][option] + '</option>';
                                }
                                hidden += '</select>';
                                cell.innerHTML = hidden;
                                has_selector = true;
                            }
                            else if(col.modifier){
                                if(col.modifier == 'enum'){
                                    hidden = hidden+'<select class="form-select" id="'+key+'">';
                                    for(var option in window[key]){
                                        hidden += row.data()[key] == option ?
                                            '<option value=' + option + ' selected>' + window[key][option] + '</option>' :
                                            '<option value=' + option + '>' + window[key][option] + '</option>';
                                    }
                                    hidden += '</select>';
                                    cell.innerHTML = hidden;
                                }
                                else if(col.modifier == 'date'){
                                    //date input
                                    cell.innerHTML = hidden+'<input id="'+key+'" type="date" class="form-control date-editor" value="'+row.data()[key]+'" >';
                                    has_date = true;
                                }
                                else{
                                    //number input
                                    cell.innerHTML = hidden+'<input id="'+key+'" type="number" class="form-control" value="'+row.data()[key]+'" >';
                                }
                            }
                            else{
                                //text input
                                cell.innerHTML = hidden+'<input id="'+key+'" type="text" class="form-control" value="'+row.data()[key]+'" >';
                            }
                        }
                        colnum+=1;
                    }
                    if(has_date){
                        //$('input.date-editor').bootstrapMaterialDatePicker({
				        //	time: false        
				        //});
                    }
                    if(has_selector){
                        $('select.db-select').select2({theme: 'bootstrap4',allowClear: true,placeholder:"select"});
                    }
                });
                
                $table.on('click', '.cancel-btn', function(event) {
                    this.parentElement.style.display = 'none';
                    this.parentElement.parentElement.children[0].style.display = 'flex';
                    var elements = document.querySelectorAll('.edit-cancel-recover');
                    for (var i = 0; i < elements.length; i++) {
                        elements[i].parentElement.innerHTML = elements[i].innerHTML;
                    }
                });
                
                $table.on('click', '.save-btn', function(event) {
                    var to_save = {};
                    var row = table.row($(this).parents('tr')).data();
                    for(var key in options.columns){
                        var col = options.columns[key];
                        if(col.editable){
                            if(col.table && col.column){
                                var val = document.getElementById(key).value;
                                val = val==""?0:val;
                                to_save[key] = val;
                            }
                            else{
                                to_save[key] = document.getElementById(key).value;
                            }
                            
                        }
                    }
                    
                    $.ajax({
                        url: '/table/'+options.id+'/save',
                        type: 'POST',
                        data: {
                            id: row.id,
                            args: to_save,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            table.ajax.reload(null, false);
                        },
                    });
                });
                
                table.on('xhr', function (e, settings, json, xhr) {
                    console.log(json);
                    if(json["selectors"]){
                        selectors = json["selectors"];
                    }
                });
            }
            
            if(options.filters){
                $('.filter-button').on('click', function () {
                    const clickedButton = $(this);
                    var buttons = document.getElementsByClassName('filter-button');
                    console.log(this.classList);
                    var is_on = clickedButton.hasClass("filter-on");
                    $('.filter-button').removeClass('filter-on');
                    if(!is_on){
                        clickedButton.addClass("filter-on");
                        filter = this.id.substring(11);
                    }
                    else{
                        filter = '';
                    }
                    console.log(filter);
                    //table.ajax.reload(null, false);
                });
            }
        });
    };
})(jQuery);