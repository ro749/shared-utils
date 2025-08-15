(function ($) {
    $.fn.layeredSmartTable = function (options = {}) {
        return this.each(function () {
            const $table = $(this);
            let tableInstance = null;
            let layer = 0;
            function initTable(metadata) {
                if (tableInstance) {
                    tableInstance.destroy();
                    $table.empty();
                }
                else{
                    console.log("no tableInstance");
                }
                
                create_html(metadata);

                // Inicializamos tu wrapper smartTable
                $table.smartTable(metadata);

                // Guardamos la instancia de DataTable para futuras manipulaciones
                tableInstance = $table.smartTable();
                console.log(tableInstance);
            }

            function fetchAndInit() {
                $.getJSON('/table/' + options.id + '/metadata/'+layer, function (metadata) {
                    initTable(metadata);
                });
            }

            function create_html(metadata){
                let theadHtml = '<thead><tr>';
                let tfootHtml = '<tfoot><tr>';
                metadata.columns.forEach(col => {
                    theadHtml += `<th>${col.display}</th>`;
                    tfootHtml += `<th>${col.display}</th>`;
                });
            
                theadHtml += '<th></th>';
                tfootHtml += '<th></th>';

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