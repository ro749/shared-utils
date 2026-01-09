(function ($) {
    function extract_number(value) {
        const numero = parseFloat(value.toString().replace(/[^0-9.-]/g, ''));
        return isNaN(numero) ? 0 : numero;
    }

    $.fn.get_number = function (value) {
        if ($(this[0]).is('input')) {
            return extract_number($(this[0]).val());
        }
        else{
            return extract_number($(this[0]).text());
        }
    }

    $.fn.get_real_value= function () {
        if ($(this[0]).is('input')) {
            if(typeof Alpine !== 'undefined'){
                var form = $(this[0]).closest('[x-data]');
                var alpine_form = Alpine.$data(form[0]);
                return alpine_form.form[$(this).attr('id').replace(/-/g, '_')];
            }
        }
    }

    $.fn.set_percent = function (value) {
        var val = Number(Number(value).toFixed(2));
        return this.each(function () {
            if ($(this).is('input')) {
                if(typeof Alpine !== 'undefined'){
                    var form = $(this).closest('[x-data]');
                    var alpine_form = Alpine.$data(form[0]);
                    alpine_form.form[$(this).attr('id').replace(/-/g, '_')] = value;
                }
                $(this).val(val + '%');
            }
            else {
                $(this).html(val + '%');
            }
        });
    }

    $.fn.set_money = function (raw_value) {
        var value = Number(Number(raw_value).toFixed(2));
        var val = value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        return this.each(function () {
            if ($(this).is('input')) {
                if(typeof Alpine !== 'undefined'){
                    var form = $(this).closest('[x-data]');
                    var alpine_form = Alpine.$data(form[0]);
                    var id_path = $(this).attr('id').split('-');
                    var field = alpine_form.form;
                    for (var i = 0; i < id_path.length-1; i++) {
                        if (!(id_path[i] in field)) {
                            field[id_path[i]] = {};
                        }
                        field = field[id_path[i]];
                    }
                    field[id_path[i]] = value;
                }
                $(this).val('$'+val);
                
            }
            else {
                $(this).html('$'+val+$.fn.set_money.defaults.suffix);
            }
        });
    }

    $.fn.set_money.defaults = {
        prefix: '$',
        suffix: '',
        decimals: 2
    };

    $.fn.set_value = function (value) {
        return this.each(function () {
            if ($(this).is('input')) {
                if($(this).hasClass('input-money')) {
                    $(this).set_money(value);
                }
                else if($(this).hasClass('input-percent')) {
                    $(this).set_percent(value);
                }
                else {
                    $(this).val(value);
                }
            }
            else {
                $(this).html(value);
            }
        });
    }

    $.fn.percent_input = function () {
        return this.each(function () {
            $(this).on('focus click', function (e) {
                if ($(this).val() === '0%') {
                    e.preventDefault();
                    this.setSelectionRange(1, 1);
                }
            });
            $(this).on('keydown', function (e) {
                var charToDelete = '';
                if (e.keyCode === 8) { // backspace key
                    var val = $(this).val();
                    var start = $(this).get(0).selectionStart;
                    var end = $(this).get(0).selectionEnd;
                    if (start === end) {
                        charToDelete = val.charAt(start - 1);
                        if (charToDelete !== '' && isNaN(charToDelete) && charToDelete !== '.') {
                            $(this).get(0).setSelectionRange(start - 1, start - 1);
                        }
                    }
                }
            });
            $(this).on('input', function () {
                $(this).set_percent(extract_number($(this).val()));
            })
        });
    }

    $.fn.money_input = function () {
        return this.each(function () {
            $(this).on('focus click', function (e) {
                if ($(this).val() === '$0') {
                    e.preventDefault();
                    this.setSelectionRange(2, 2);
                }
            });
            $(this).on('keydown', function (e) {
                var charToDelete = '';
                if (e.keyCode === 8) { // backspace key
                    var val = $(this).val();
                    var start = $(this).get(0).selectionStart;
                    var end = $(this).get(0).selectionEnd;
                    if (start === end) {
                        charToDelete = val.charAt(start - 1);
                        if (charToDelete !== '' && isNaN(charToDelete) && charToDelete !== '.') {
                            $(this).get(0).setSelectionRange(start - 1, start - 1);
                        }
                    }
                }
            });
            $(this).on('input', function () {
                $(this).set_money($(this).get_number());
            });

        });
    }

    $.fn.init_string_utils = function () {
        $(document).on('input','.input-percent', function(e) {
            $(this).set_percent($(this).get_number());
        });
        $(document).on('input','.input-money', function(e) {
            var text = $(this).val();
            $(this).set_money($(this).get_number());
            var position = $(this).data('position');
            var current_count = 0;
            var current_position = 0;
            
            while(current_count != position && current_position <= text.length){
                var char = text[current_position];
                if(char == '.' || isDigit(char) ){
                    current_count+=1;
                }
                current_position+=1;
            }
            if(current_count == position ){
                $(this).get(0).setSelectionRange(current_position,current_position);
            }
            if($(this).data('prev_value') == '$0.00'){
                $(this).get(0).setSelectionRange(text.length-3,text.length-3);
            }
        });
        $(document).on('focus click','.input-percent', function (e) {
            if ($(this).val() === '0%' || $(this).val() === '0.00%') {
                e.preventDefault();
                this.setSelectionRange(1, 1);
            }else{
                var length = $(this).val().length;
                var start = $(this).get(0).selectionStart;
                if(length==start){
                    e.preventDefault();
                    this.setSelectionRange(length-1, length-1);
                }
            }
        });
        $(document).on('focus click','.input-money', function (e) {
            if ($(this).val() === '$0' || $(this).val() === '$0.00') {
                e.preventDefault();
                this.setSelectionRange(2, 2);
            }
            else{
                var length = $(this).val().length;
                var start = $(this).get(0).selectionStart;
                if(length-start <= 2){
                    e.preventDefault();
                    this.setSelectionRange(length-3, length-3);
                }
            }
        });
        $(document).on('keydown','.input-money', function(e) {
            var start = $(this).get(0).selectionStart;
            
            var count = 0;
            var value = $(this).val();
            $(this).data('prev_value',value);
            for(var i=start-1; i>=0; i--){
                if(value[i] === '.' || !isNaN(value[i])){
                    count++;
                }
            }
            
            if (e.keyCode >= 48 && e.keyCode <= 57) {
                $(this).data('position',count+1);
            } 
            var charToDelete = '';
            if (e.keyCode === 8) { // backspace key
                $(this).data('position',count-1);
                var val = $(this).val();
                var start = $(this).get(0).selectionStart;
                var end = $(this).get(0).selectionEnd;
                if (start === end) {
                    charToDelete = val.charAt(start - 1);
                    if (charToDelete !== '' && isNaN(charToDelete) && charToDelete !== '.') {
                        $(this).get(0).setSelectionRange(start - 1, start - 1);
                    }
                }
            }
        });
    }
})(jQuery);