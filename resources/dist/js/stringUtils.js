(function ($) {
    function extract_number(value) {
        const numero = parseFloat(value.toString().replace(/[^0-9.-]/g, ''));
        return isNaN(numero) ? 0 : numero;
    }

    $.fn.get_number = function (value) {
        return extract_number($(this[0]).val());
    }

    $.fn.set_percent = function (value) {
        var val = Number(value.toFixed(2));
        if(val > 100){
            val = 100;
        }
        if(val < 0){
            val = 0;
        }
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
        var val = value.toLocaleString('en-US');
        return this.each(function () {
            if ($(this).is('input')) {
                if(typeof Alpine !== 'undefined'){
                    var form = $(this).closest('[x-data]');
                    var alpine_form = Alpine.$data(form[0]);
                    alpine_form.form[$(this).attr('id').replace(/-/g, '_')] = value;
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

    $.fn.percent_input = function () {
        return this.each(function () {
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
                $(this).set_money(extract_number($(this).val()));
            })
        });
    }
})(jQuery);