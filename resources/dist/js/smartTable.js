(function ($) {
    $.fn.smartTable = function (options = {}) {
        function cancer_edit(){
            $('.edit-buttons').css('display', 'none');
            $('.normal-buttons').css('display', 'flex');
            $('.edit-cancel-recover').each(function(){
                $(this).parent().html($(this).html());
            });
            var editing = document.getElementById("editing");
            if(editing) {
                editing.removeAttribute("id");
                editing.removeAttribute("x-data");
            }
        }
        return this.each(function () {
            const $table = $(this);
            var selectors = [];
            const columns = [];
            for (let col in options.columns){
                let column = { data: col };
                if(options.columns[col].logic_modifier) {
                    switch (options.columns[col].logic_modifier.type) {
                        case 'options':
                            column.render = (data) => window[options.columns[col].logic_modifier.options][data];
                            break;
                    }
                }
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
                            buttons += `
                                <button type="button" class="btn view-btn w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <iconify-icon icon="iconamoon:eye-light">
                                    </iconify-icon>
                                </button>
                            `;
                        }

                        if (options.is_editable || options.edit_url) {
                            buttons += `
                                <button type="button" class="btn edit-btn w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <iconify-icon icon="lucide:edit">
                                    </iconify-icon>
                                </button>
                            `;
                        }

                        if (options.delete) {
                            buttons += `
                                <button type="button" class="btn delete-btn w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <iconify-icon icon="mingcute:delete-2-line">
                                    </iconify-icon>
                                </button>
                            `;
                        }
                        buttons = '<div class="normal-buttons" style="display:flex">'+buttons+'</div>';
                        if (options.is_editable) {
                            buttons += `
                                <div class="edit-buttons" style="display:none">
                                    <button type="button" class="btn save-btn w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                        <iconify-icon icon="iconamoon:check-circle-1">
                                        </iconify-icon>
                                    </button>
                                    <div style="width:6px"></div>
                                    <button type="button" class="btn cancel-btn w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                        <iconify-icon icon="iconamoon:close-circle-1">
                                        </iconify-icon>
                                    </button>
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
            for (const [key, filter] of Object.entries(options.filters)){
                if (filter.session) {
                    const sessionValue = sessionStorage.getItem(filter.session)??1;
                    filters["cf-"+key] = sessionValue;
                } 
            }
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
                serverSide: true,
                initComplete: function () {
                    for (const [key, filter] of Object.entries(options.filters)) {
                        const selector = document.createElement('div');
                        selector.classList.add('filter');
                        selector.style.display = 'flex';
                        selector.style.flexDirection = 'row';
                        selector.style.justifyContent = 'flex-end';
                        selector.style.gap = '8px';
                        selector.style.marginLeft = '8px';
                        selector.style.alignItems = 'center';

                        const p = document.createElement('p');
                        p.style.margin = '0';
                        p.textContent = filter.display;
                        selector.appendChild(p);

                        const select = document.createElement('select');
                        select.id = "cf-"+key;
                        select.classList.add(filter.class);
                        select.classList.add('form-select');
                        select.classList.add('w-auto');
                        select.classList.add('category-filter');
                        for (const [option_key, option] of Object.entries(filter.selector.options)) {
                            const optionElement = document.createElement('option');
                            optionElement.value = option_key;
                            optionElement.textContent = option;
                            select.appendChild(optionElement);
                        }
                        selector.appendChild(select);
                        if(filter.session!=""){
                            const sessionValue = sessionStorage.getItem(filter.session)??1;
                            if (sessionValue) {
                                select.value = sessionValue;
                                filters[select.id] = sessionValue;
                            }
                            select.addEventListener('change', function () {
                                sessionStorage.setItem(filter.session, this.value);
                            });
                        }
                        
                        $("#"+options.id+"_filter").prepend(selector);
                    }
                    $("#"+options.id+"_filter").css("display", "flex").css("flex-direction", "row").css("gap", "6px");
                    setTimeout(function() {
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
                    }, 0);
                    
                }
            });
            
            
            
            if(options.view){
                $table.on('click', '.view-btn', function(event) {
                    window.location.href = options.view.url+'?'+options.view.name+'='+table.row($(this).parents('tr')).data()[options.view.param];
                });
            }

            if(options.edit_url){
                $table.on('click', '.edit-btn', function(event) {
                    window.location.href = options.edit_url.url+'?'+options.edit_url.name+'='+table.row($(this).parents('tr')).data()[options.edit_url.param];
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
                    cancer_edit();
                    this.parentElement.style.display = 'none';
                    this.parentElement.parentElement.children[1].style.display = 'flex';
                    this.parentElement.parentElement.children[1].style.justifyContent = 'left';
                    var row = table.row($(this).parents('tr'));
                    var initial_data = "id: '"+row.data().id+"',";
                    for(var key in options.columns){
                        var col = options.columns[key];
                        if(col.editable){
                            initial_data += key+": "+"'"+row.data()[key]+"',";
                        }
                    }
                    initial_data = initial_data.slice(0, -1);
                    this.parentElement.parentElement.parentElement.setAttribute('x-data', '{ form: { '+initial_data+' },errors: {} }');
                    this.parentElement.parentElement.parentElement.id = "editing";
                    var colnum = 0;
                    var has_date = false;
                    var has_selector = false;
                    for(var key in options.columns){
                        var col = options.columns[key];
                        if(col.editable){
                            var cell = row.node().getElementsByTagName('td')[colnum];
                            var hidden = "<span style='display:none' class='edit-cancel-recover'>"+cell.innerHTML+"</span>";
                            if(col.logic_modifier != null){
                                switch(col.logic_modifier.type){
                                    case 'foreign_key':
                                        hidden = hidden+'<select x-model="form.'+key+'" class="form-select db-select" id="'+key+'"><option disabled selected value>selec</option>';
                                        for(var option in selectors[key]){
                                            hidden += '<option value=' + option + '>' + selectors[key][option] + '</option>';
                                        }
                                        hidden += '</select>';
                                        cell.innerHTML = hidden;
                                        has_selector = true;
                                        break;
                                    case 'options':
                                        hidden = hidden+'<select x-model="form.'+key+'" class="form-select" id="'+key+'">';
                                        for(var option in window[col.logic_modifier.options]){
                                            hidden += '<option value=' + option + '>' + window[col.logic_modifier.options][option] + '</option>';
                                        }
                                        hidden += '</select>';
                                        cell.innerHTML = hidden;
                                        break;
                                }
                            }
                            else if(col.modifier != null){
                                switch(col.modifier){
                                    case 'date':
                                        cell.innerHTML = hidden+'<input x-model="form.'+key+'" id="'+key+'" type="date" class="form-control date-editor" >';
                                        has_date = true;
                                        break;
                                    case 'number':
                                        cell.innerHTML = hidden+'<input x-model="form.'+key+'" id="'+key+'" type="number" class="form-control" >';
                                        break;
                                }
                            }
                            else{
                                //text input
                                cell.innerHTML = hidden+'<input x-model="form.'+key+'" id="'+key+'" type="text" class="form-control" value="'+row.data()[key]+'" >';
                            }
                        }
                        cell.innerHTML += '<p class="form-error" x-text="errors[\''+key+'\']"></p>';
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
                    cancer_edit();
                });
                
                $table.on('click', '.save-btn', function(event) {
                    var editing = document.getElementById("editing");
                    const alpine_data = Alpine.$data(editing);
                    $.ajax({
                        url: '/table/'+options.id+'/save',
                        type: 'POST',
                        data: alpine_data.form,
                        success: function(response) {
                            table.ajax.reload(null, false);
                        },
                        error: (xhr) => {
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                alpine_data.errors = xhr.responseJSON.errors;
                            } else {
                                alpine_data.errors = { general: 'An error occurred. Please try again.' };
                            }
                        },
                    });
                });
            }
        });
    };
})(jQuery);