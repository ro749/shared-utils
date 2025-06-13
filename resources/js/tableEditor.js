function create_input_text(input){
    return '<input id="'+input['id']+'" type="text" class="form-control" value="'+input['value']+'" >';
}

function create_input_number(input){
    var value = numWithoutComas(input['value']);
    return '<input id="'+input['id']+'" type="number" class="form-control" value="'+value+'" >';
}

function create_date_editor(input){
    return '<input id="'+input['id']+'" type="date" class="form-control date-editor" value="'+input['value']+'" >';
}

function create_selector(input){
    var selector = '<select class="'+(input["className"]??"form-select ")+' '+(input["db"]?'db-select':'')+'" name="'+input["id"]+'" id="'+input["id"]+'" style="'+input["style"]+'">';
    var options = input["options"];
    var values = input["values"];
    var data = input["value"]??"";
    for (var i = 0; i < options.length; ++i)
    {
        selector += data == options[i] ?
                                '<option value=' + (values!=null?values[i]:i) + ' selected>' + options[i] + '</option>' :
                                '<option value=' + (values!=null?values[i]:i) + '>' + options[i] + '</option>';
    }
    selector += '</select>';
    return selector;
}

function table_editor(input){
    var cell = input["row"].node().getElementsByTagName('td')[input["col"]];
    input["db"] = input["db"]??false;
    if(input["options"] != null){
        cell.innerHTML = "<span style='display:none' class='edit-cancel-recover'>"+cell.innerHTML+"</span>"+input["create"]({"id": "edit"+input["col"], "value": cell.innerHTML, "options": input["options"], "db": input["db"], "values": input["values"]});
    }
    else{
        cell.innerHTML = "<span style='display:none' class='edit-cancel-recover'>"+cell.innerHTML+"</span>"+input["create"]({"id": "edit"+input["col"], "value": cell.innerHTML,"date":input["date"]});
    }
}