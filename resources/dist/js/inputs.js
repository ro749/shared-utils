(function($) {
    const defaults = {
        text: {
            type: 'text', 
            class: 'form-control'
        },
        number: {
            type: 'number', 
            class: 'form-control no-arrows'
        },

        quantity: {
            type: 'number', 
            class: 'form-control',
            min: 1,
            step: 1
        },
        id_numer:{
            type: 'text', 
            class: 'form-control',
            oninput: "this.value = this.value.replace(/\\D/g, '')"
        }
    };

    // Objeto para almacenar funciones de generación de inputs
    const InputFactory = {
        // Función principal para crear un input genérico
        createInput: function(config) {

            const configDefault = defaults[config.type];
            delete config.type;
            const settings = $.extend({}, configDefault, config);
            return $('<input>', settings);
        },

        // Función para crear un select
        
    };

    // Exponer las funciones globalmente
    window.InputFactory = InputFactory;

    // Opcional: Extender jQuery para uso directo
    $.fn.createInput = function(config) {
        const $input = InputFactory.createInputGroup(config);
        this.append($input);
        return this;
    };
})(jQuery);