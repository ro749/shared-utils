(function ($) {
    $.fn.set_dates = function (start_date, end_date) {
        $(this).data('start_date', start_date);
        $(this).data('end_date', end_date);
    }
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
                let col_data = options.columns[col];
                let renderFn = null;
                if(col_data.logic_modifier) {
                    switch (col_data.logic_modifier.type) {
                        case 'options':
                            renderFn = (data) => {
                                if(data == null){
                                    return '';
                                }
                                return '<div class="'+col_data.logic_modifier.options+'-'+data+'">'+
                                window[col_data.logic_modifier.options][data]+
                                '</div>';
                            }
                            break;
                        case 'foreign_key':
                            if(options?.form && options.form.fields && options.form.fields[col]) {
                                renderFn = (data) => data==0||data==null?"":selectors[col][data];
                            }
                            break;
                    }
                }
                switch (col_data.modifier) {
                    case 'money':
                        column.render = (data) => {
                            let value = renderFn ? renderFn(data) : data;
                            if(value == null || value === '') {
                                return '';
                            }
                            return `$${Number(value).toLocaleString('es-MX')}`+$.fn.set_money.defaults.suffix;
                        }
                        break;
                    case 'meters':
                        column.render = (data) => {
                            let value = renderFn ? renderFn(data) : data;
                            return `${value} m²`;
                        }
                        break;
                    case 'percent':
                        column.render = (data) => {
                            let value = renderFn ? renderFn(data) : data;
                            return `${value}%`;
                        }
                        break;
                    case 'date':
                        column.render = (data) => {
                            let value = renderFn ? renderFn(data) : data;
                            value = value.split('T')[0];
                            const [y, m, d] = value.split("-");
                            return `${m}/${d}/${y}`;
                        }
                        break;
                    case '':
                    case null:
                        column.render = (data) => {
                            return renderFn ? renderFn(data) : '<p class="data-'+col+'">'+data+'</p>';
                        }
                        break;
                    
                }
                //if(col_data.table && options.columns[col].column && options.columns[col].editable){
                //    column.render = (data) => data==0?"":selectors[col][data];
                //}
                columns.push(column);
            }
            if (options.needs_buttons) {
                columns.push({
                    data: null,
                    render: function (data, type, row, meta) {
                        let buttons = '';
                        for(var button in options.buttons){
                            buttons += `
                                <button type="button" class="btn `+options.buttons[button].button_class+` w-32-px h-32-px `+options.buttons[button].background_color_class+' '+options.buttons[button].text_color_class+` rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <iconify-icon icon="`+options.buttons[button].icon+`"> 
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
            var filters = options.manual_filters??{};
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
            if(options.needs_selectors){
                $.ajax({
                    url: '/table/'+options.id+'/selectors',
                    type: 'GET',
                    success: function(response) {
                        selectors = response;
                    }
                });
            }
            var table = $table.DataTable({
                ajax: {
                    url: '/table/'+options.id+'/get'+(options.layer!=null?'/'+options.layer:''),
                    type: 'GET',
                    data: function (d) {
                        d.filters = filters; 
                        let start_date = $table.data('start_date');
                        let end_date = $table.data('end_date');
                        if(start_date && end_date){
                            d.start_date = start_date;
                            d.end_date = end_date;
                        }
                    },
                    
                },
                columns: columns,
                serverSide: true,
                order: options.order?[options.order]:[],
                pageLength: options.page_length??10,
                language: options.texts,
                deferLoading: options.autoload?null:0,
                initComplete: function () {
                    for (const [key, filter] of Object.entries(options.filters)) {
                        
                        if(filter.selector){
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

                            $("#"+options.id+"_wrapper .dt-layout-cell.dt-end").first().prepend(selector);
                        }
                        else{
                            const filters_div = document.createElement('div');
                            filters_div.classList.add('filter');
                            filters_div.style.display = 'flex';
                            filters_div.style.flexDirection = 'row';
                            filters_div.style.justifyContent = 'flex-end';
                            filters_div.style.gap = '8px';
                            filters_div.style.marginLeft = '8px';
                            filters_div.style.alignItems = 'center';
                            filters_div.id = key;
                            const p = document.createElement('p');
                            p.style.margin = '0';
                            p.textContent = filter.display;
                            filters_div.appendChild(p);
                            for(let f in filter.filters){
                                let filter_button = filter.filters[f];
                                const button = document.createElement('button');
                                button.id = "f-"+key+"-"+f;
                                button.classList.add('filter-button');
                                button.classList.add('btn');
                                button.classList.add('btn-outline-neutral-900');
                                button.classList.add('no-hover');
                                button.textContent = filter_button.display;
                                filters_div.appendChild(button);
                            }
                            $("#"+options.id+"_wrapper .dt-layout-cell.dt-end").first().prepend(filters_div);
                        }
                    }
                    $("#"+options.id+"_wrapper .dt-layout-cell.dt-end").first().css("display", "flex").css("flex-direction", "row").css("justify-content", "flex-end").css("gap", "6px");
                    setTimeout(function() {
                        if(options.filters!=[]){
                            $('.filter-button').on('click', function () {
                                const clickedButton = $(this);
                                var is_on = clickedButton.hasClass("active");
                                $('.filter-button').removeClass('active');
                                if(!is_on){
                                    clickedButton.addClass("active");
                                    filters[this.parentElement.id] = this.id.substring(10);
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
            for(let button_num in options.buttons){
                let button = options.buttons[button_num];
                if(button.view != null){
                    $table.on('click', '.'+button.button_class, function(event) {
                        window.location.href = 
                        button.view.url+'?'+
                        button.view.name+'='+
                        table.row($(this).parents('tr')).data()[button.view.param];
                    });
                }
                if(button.warning != null){
                    $table.on('click', '.'+button.button_class, function(event) {
                        let row = table.row($(this).parents('tr')).data();
                        let warning = button.warning;
                        const matches = [...warning.matchAll(/\{(.*?)\}/g)];
                        const args = matches.map(match => match[1].trim());
                        for (const arg of args) {
                            warning = warning.replace('{' + arg + '}', row[arg]);
                        }
                        $('#'+button.button_class+'-warning').text(warning);
                        openPopup(button.button_class+'-warning-popup');
                        $('#on-'+button.button_class)[0].onclick = function(){
                            $.ajax({
                                url: button.url,
                                type: 'POST',
                                data: {
                                    id: row.id,
                                    filters: filters,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    if(button.reload){
                                        table.ajax.reload(null, false);
                                    }
                                    closePopup(button.button_class+'-warning-popup');
                                    if(button.success != null){
                                        let success = button.success;
                                        const matches = [...success.matchAll(/\{(.*?)\}/g)];
                                        const args = matches.map(match => match[1].trim());
                                        for (const arg of args) {
                                            success = success.replace('{' + arg + '}', row[arg]);
                                        }
                                        $('#'+button.button_class+'-success').text(success);
                                        openPopup(button.button_class+'-success-popup');
                                    }
                                },
                            });
                        };
                        openPopup(button.button_class+'-popup');
                    });
                }
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
            }
            
            if(options.is_editable){
                $table.on('click', '.edit-btn', async function(event) {
                    cancer_edit();
                    this.parentElement.style.display = 'none';
                    this.parentElement.parentElement.children[1].style.display = 'flex';
                    this.parentElement.parentElement.children[1].style.justifyContent = 'left';
                    var row = table.row($(this).parents('tr'));
                    var initial_data = "id: '"+row.data().id+"',";
                    for(var key in options.form.fields){
                        initial_data += key+": "+"'"+row.data()[key]+"',";
                    }
                    initial_data = initial_data.slice(0, -1);
                    var xdata = [];
                    xdata.push('form: { '+initial_data+' },');
                    xdata.push('errors: {},');
                    xdata.push('init(){');
                    for(var key_field in options.form.fields){
                        var column = options.columns[key_field];
                        if(column?.logic_modifier && column.logic_modifier.type == 'foreign_key'){
                            xdata.push("$('#"+key_field+"').val(this.form."+key_field+").select2({theme: 'bootstrap-5',width: 'auto',allowClear: true,placeholder: 'Select'});");
                            xdata.push("$('#"+key_field+"').on('change', () => {");
                            xdata.push("this.form."+key_field+" = $('#"+key_field+"').val();");
                            xdata.push("});");
                        }
                    }
                    xdata.push('},');
                    
                    this.parentElement.parentElement.parentElement.id = "editing";
                    var colnum = 0;
                    var changes = [];
                    for(let key in options.columns){
                        var col = options.columns[key];
                        if(options.form.fields[key]){
                            let cell = row.node().getElementsByTagName('td')[colnum];
                            let field = options.form.fields[key];
                            let hidden = "<span style='display:none' class='edit-cancel-recover'>"+cell.innerHTML+"</span>";
                            if(col.logic_modifier != null){
                                switch(col.logic_modifier.type){
                                    case 'foreign_key':
                                        if(field.hot_reload!=""){
                                            var data = await $.ajax({
                                                url: field.hot_reload,
                                                type: "GET",
                                                dataType: "json",
                                                data: {
                                                    id: row.data().id
                                                }
                                            });
                                            hidden = hidden+'<select x-model="form.'+key+'" class="form-select db-select" id="'+key+'"><option disabled selected value>selec</option>';
                                            for(let option in data){
                                                hidden += '<option value=' + data[option].id + '>' + data[option].value + '</option>';
                                            }
                                            hidden += '</select>';
                                        }
                                        else{
                                            hidden = hidden+'<select x-model="form.'+key+'" class="form-select db-select" id="'+key+'"><option disabled selected value>selec</option>';
                                            for(var option in selectors[key]){
                                                hidden += '<option value=' + option + '>' + selectors[key][option] + '</option>';
                                            }
                                            hidden += '</select>';
                                        }
                                        
                                        break;
                                    case 'options':
                                        hidden = hidden+'<select x-model="form.'+key+'" class="form-select" id="'+key+'">';
                                        for(var option in window[col.logic_modifier.options]){
                                            hidden += '<option value=' + option + '>' + window[col.logic_modifier.options][option] + '</option>';
                                        }
                                        hidden += '</select>';
                                        
                                        break;
                                }
                            }
                            else{
                                switch(field.type){
                                    case 'date':
                                        hidden = hidden+'<input x-model="form.'+key+'" id="'+key+'" type="date" class="form-control date-editor" >';
                                        break;
                                    case 'number':
                                        hidden = hidden+'<input x-model="form.'+key+'" id="'+key+'" type="number" class="form-control" >';
                                        break;
                                    case 'tel':
                                        hidden = hidden+'<input x-model="form.'+key+'" id="'+key+'" type="tel" class="form-control">';
                                        break;
                                    case 'email':
                                        hidden = hidden+'<input x-model="form.'+key+'" id="'+key+'" type="text" class="form-control" oninput="this.value = this.value.toLowerCase()" >';
                                        break;
                                    case 'money':
                                        hidden = hidden+'<input x-model="form.'+key+'" id="'+key+'" type="text" class="form-control input-money" >';
                                        break;
                                    case 'text':
                                        hidden = hidden+'<input x-model="form.'+key+'" id="'+key+'" type="text" class="form-control" >';
                                        break;
                                }
                            }
                            if(field.type == 'hidden'){
                                continue;
                            }
                            hidden += '<p class="form-error" x-text="errors[\''+key+'\']"></p>';
                            changes.push({key: colnum, value: hidden});
                        }
                        colnum+=1;
                    }
                    for(let i in changes){
                        let change = changes[i];
                        var cell = row.node().getElementsByTagName('td')[change.key];
                        cell.innerHTML = change.value;
                    }
                    this.parentElement.parentElement.parentElement.setAttribute(
                        'x-data',
                        '{ ' + xdata.join(' ') + ' }'
                    );
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