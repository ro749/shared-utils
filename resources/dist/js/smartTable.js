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
            var filters = {};
            var params = new URLSearchParams(window.location.search);
            params.forEach((value, key) => {
                filters[key] = value;
            });
            $.ajax({
                url: '/table/'+options.id+'/selectors',
                type: 'GET',
                success: function(response) {
                    selectors = response;
                }
            })
            var table = $table.DataTable({
                ajax: {
                    url: '/table/'+options.id+'/get',
                    type: 'GET',
                    data: function (d) {
                        d.filters = filters; 
                    }
                },
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
                                filters: filters,
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
                            if(col.modifier != null){
                                switch(col.modifier){
                                    case 'foreign_key':
                                        hidden = hidden+'<select class="form-select db-select" id="'+key+'"><option disabled selected value>selec</option>';
                                        for(var option in selectors[key]){
                                            hidden += row.data()[key] == option ?
                                                '<option value=' + option + ' selected>' + selectors[key][option] + '</option>' :
                                                '<option value=' + option + '>' + selectors[key][option] + '</option>';
                                        }
                                        hidden += '</select>';
                                        cell.innerHTML = hidden;
                                        has_selector = true;
                                        break;
                                    case 'enum':
                                        hidden = hidden+'<select class="form-select" id="'+key+'">';
                                        for(var option in window[key]){
                                            hidden += row.data()[key] == option ?
                                                '<option value=' + option + ' selected>' + window[col.options][option] + '</option>' :
                                                '<option value=' + option + '>' + window[col.options][option] + '</option>';
                                        }
                                        hidden += '</select>';
                                        cell.innerHTML = hidden;
                                        break;
                                    case 'date':
                                        cell.innerHTML = hidden+'<input id="'+key+'" type="date" class="form-control date-editor" value="'+row.data()[key]+'" >';
                                        has_date = true;
                                        break;
                                    case 'number':
                                        cell.innerHTML = hidden+'<input id="'+key+'" type="number" class="form-control" value="'+row.data()[key]+'" >';
                                        break;
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
            }
            
            if(options.filters!=[]){
                $('.filter-button').on('click', function () {
                    const clickedButton = $(this);
                    var is_on = clickedButton.hasClass("filter-on");
                    $('.filter-button').removeClass('filter-on');
                    if(!is_on){
                        clickedButton.addClass("filter-on");
                        filters[this.parentElement.id] = this.id.substring(11);
                    }
                    else{
                        delete filters[this.parentElement.id];
                    }
                    table.ajax.reload(null, false);
                });
                $('.category-filter').on('change', function () {
                    if (this.value == "") {
                        delete filters[this.id];
                    } else {
                        filters[this.id] = this.value;
                    }
                    table.ajax.reload(null, false);
                });
                $('.date-filter').on('change', function () {
                    if (this.value == "") {
                        delete filters[this.id];
                    } else {
                        filters[this.id] = this.value;
                    }
                    table.ajax.reload(null, false);
                });
            }
        });
    };
})(jQuery);