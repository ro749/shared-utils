(function ($) {
    $.fn.smartDynamicTable = function (options = {}) {
        return this.each(function () {
            const $table = $(this);
            let currentColumnsJson = null;
            let tableInstance = null;

            function initTable(columnsMeta) {
                // Si ya existe una tabla, la destruimos y limpiamos
                if (tableInstance) {
                    tableInstance.destroy();
                    $table.empty();
                }

                currentColumnsJson = JSON.stringify(columnsMeta);
                create_html(columnsMeta);
                // Llamamos a smartTable con las columnas dinámicas
                options.columns = {};

                // Reconvertir el array de columnas meta a objeto con keys según tu lógica original
                // Aquí asumimos que columnsMeta es un array [{ data: "col1", ... }, ...]
                // y smartTable espera objeto { col1: { label: "...", ... } }
                columnsMeta.forEach(col => {
                    options.columns[col.data] = {
                        modifier: col.modifier,
                        logic_modifier: col.logic_modifier,
                        editable: col.editable
                    };
                });



                // Inicializamos tu wrapper smartTable
                $table.smartTable(columnsMeta);

                // Guardamos la instancia de DataTable para futuras manipulaciones
                tableInstance = $table.DataTable();
            }

            function fetchAndInit() {
                $.getJSON('/table/' + options.id + '/columns', function (metaColumns) {
                    const newColumnsJson = JSON.stringify(metaColumns);
                    if (currentColumnsJson !== newColumnsJson) {
                        initTable(metaColumns);
                    } else if (tableInstance) {
                        tableInstance.ajax.reload();
                    } else {
                        initTable(metaColumns);
                    }
                });
            }

            function create_html(columnsMeta){
                let theadHtml = '<thead><tr>';
                let tfootHtml = '<tfoot><tr>';
                columnsMeta.forEach(col => {
                    theadHtml += `<th>${col.display}</th>`;
                    tfootHtml += `<th>${col.display}</th>`;
                });
            
                if (options.needs_buttons) {
                    theadHtml += '<th></th>';
                    tfootHtml += '<th></th>';
                }
                theadHtml += '</tr></thead>';
                tfootHtml += '</tr></tfoot>';
            
                $('#'+options.id).html(theadHtml + tfootHtml);
            }

            // Primera carga
            fetchAndInit();

            // Puedes exponer un método para refrescar columnas y datos si quieres:
            $table.data('refresh', fetchAndInit);
        });
    };
})(jQuery);