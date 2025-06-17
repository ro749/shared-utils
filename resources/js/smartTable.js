(function ($) {
    $.fn.smartTable = function (options = {}) {
        return this.each(function () {
            const $table = $(this);
            const config = $table.data('config'); // from HTML data-config or passed directly
            
            const columns = config.columns.map(col => {
                let column = { data: col.key };

                if (col.modifier) {
                    switch (col.modifier) {
                        case 'meters':
                            column.render = data => formatNumber(data) + 'm²';
                            break;
                        case 'foot':
                            column.render = data => formatNumber(data) + 'sqft';
                            break;
                        case 'money':
                        case 'dolars':
                            column.render = data => '$' + formatNumber(data);
                            break;
                        case 'percent':
                            column.render = data => formatNumber(data) + '%';
                            break;
                        case 'date':
                            column.render = data => new Date(data).toLocaleDateString();
                            break;
                        case 'enum':
                            column.render = (data, type, row, meta) => col.enumMap[data] ?? data;
                            break;
                    }
                }

                return column;
            });

            if (config.hasButtons) {
                columns.push({
                    data: null,
                    render: function (data, type, row, meta) {
                        let buttons = '';

                        if (config.view) {
                            buttons += '<button type="button" class="btn view-btn"><i class="bx bx-show"></i></button>';
                        }

                        if (config.editable) {
                            buttons += '<button type="button" class="btn edit-btn"><i class="bx bx-edit-alt"></i></button>';
                        }

                        if (config.delete) {
                            buttons += '<button type="button" class="btn delete-btn"><i class="bx bx-trash"></i></button>';
                        }

                        if (config.editable) {
                            buttons = `
                                <div class="normalbuttons" style="display:flex">${buttons}</div>
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
                ajax: config.ajax,
                columns: columns,
            });

            if(options.view){
                $table.on('click', '.view-btn', function(event) {
                    window.location.href = options.view.url+"?"+options.view.name+"="+table.row($(this).parents('tr')).data()[options.view.param];}
                );
            }

            if(options.delete){
                $table.on('click', '.delete-btn', function(event) {
                    var row = table.row($(this).parents('tr')).data();
                    delete_id = row.id;
                    var warning = options.delete.warning;
                    const matches = [...warning.matchAll(/\{(.*?)\}/g)];
                    const args = matches.map(match => match[1].trim());
                    for (const arg of args) {
                        warning = warning.replace('{' + arg + '}', row[arg]);
                    }
                    $('#delete-warning').text(warning);
                    $('#on-delete').onclick = function() {
                        $.ajax({
                            url: "/table/"+options.id+"/delete",
                            method: 'POST',
                            data: {
                                id: delete_id,
                                _token:t('meta[name="csrf-token"]').attr("content")
                            },
                            success: function(response) {
                                table.ajax.reload(null, false);
                                closePopup('delete-popup');
                            },
                        });
                    }
                    openPopup('delete-popup');
                });

                
            }
            if(options.is_editable){

                var selectors = [];

                table.on('xhr', function (e, settings, json, xhr) {
                    selectors = json["selectors"];
                });

                $('#".$input["id"]."').on('click', '.edit-btn', function(event) {
                    this.parentElement.style.display = 'none';
                    this.parentElement.parentElement.children[1].style.display = 'flex';
                    this.parentElement.parentElement.children[1].style.justifyContent = 'left';
                    var row = table.row($(this).parents('tr'));
                    var col_num = 0;
                    var select2 = false;
                    for(var key in options.columns){
                        var col = options.columns[key];
                        if(col.editable == false) continue;
                        var cell = row.node().getElementsByTagName('td')[col_num];
                        cell.innerHTML = "<span style='display:none' class='edit-cancel-recover'>"+cell.innerHTML+"</span>"; 
                        if(col.table && col.column){
                            cell.innerHTML += '<select id="'+key+'" class="form-select select2"><option disabled selected value>select</option>';
                            for(var value in selectors[key]){
                                cell.innerHTML += '<option value="'+value+'" '+(value==row.data()[key]?'selected':'')+'>'+selectors[key][value]+'</option>';
                            }
                            cell.innerHTML += '</select>';
                            select2 = true;
                        }
                        else if(col.modifier){
                            if(col.modifier == 'enum'){
                                cell.innerHTML += '<select id="'+key+'" class="form-select"><option disabled selected value>select</option>';
                                for(var value in window[key]){
                                    cell.innerHTML += '<option value="'+value+'" '+(value==row.data()[key]?'selected':'')+'>'+window[key][value]+'</option>';
                                }
                                cell.innerHTML += '</select>';
                            }
                            else if(col.modifier == 'date'){
                                cell.innerHTML += '<input id="'+key+'" type="date" class="form-control" value="'+row.data()[key]+'" ></input>';
                            }
                            else{
                                cell.innerHTML += '<input id="'+key+'" type="number" class="form-control" value="'+row.data()[key]+'" ></input>';
                            }
                        }
                        else{
                            cell.innerHTML += '<input id="'+key+'" type="text" class="form-control" value="'+row.data()[key]+'" ></input>';
                        }
                        col_num+=1;
                    }
                    if(select2){
                        $('.select2').select2({theme: 'bootstrap4'});
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
                    var row = table.row($(this).parents('tr'));
                    var id = row.data().id;
                    var args = {};
                    for(var key in options.columns){
                        if(options.columns[key].editable == false) continue;
                        args[key] = document.getElementById(key).value;
                    }
                    $.ajax({
                        url: '/table/'+options.id+'/save',
                        method: 'POST',
                        data: {
                            id: row.data().id,
                            data: data,
                            _token:t('meta[name="csrf-token"]').attr("content")
                        },
                        success: function(response) {
                            table.ajax.reload(null, false);
                        },
                    });
                });
            };

        });
    };
})(jQuery);