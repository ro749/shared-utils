(function ($) {
    $.fn.layeredSmartTable = function (options = {}) {
        return this.each(function () {
            const $table = $(this);
            
            let currentColumnsJson = null;
            let tableInstance = null;
            let layer = 0;
            let selected_layers = [];
            var labels = [];
            function initTable(metadata) {
                $('#'+options.id).DataTable().destroy();
                $table.empty();
                
                create_html(metadata);
                metadata.layer = layer;
                metadata.manual_filters = selected_layers;
                tableInstance = $table.smartTable(metadata);
            }

            function fetchAndInit() {
                $.getJSON('/table/' + options.id + '/metadata/'+layer, function (metadata) {
                    initTable(metadata);
                });
            }

            function create_html(metadata){
                let theadHtml = '<thead><tr>';
                let tfootHtml = '<tfoot><tr>';
                for(column in metadata.columns){
                    theadHtml += `<th>${metadata.columns[column].display}</th>`;
                    tfootHtml += `<th>${metadata.columns[column].display}</th>`;
                }
            
                theadHtml += '<th></th>';
                tfootHtml += '<th></th>';

                theadHtml += '</tr></thead>';
                tfootHtml += '</tr></tfoot>';
            
                $('#'+options.id).html(theadHtml + tfootHtml);
            }

            $table.on('click', '.next-btn', function(event) {
                var row = $('#'+options.id).DataTable().row($(this).parents('tr')).data();
                selected_layers[layer] = row.id;
                labels[layer] = row;
                layer+=1;
                fetchAndInit();
            });

            $table.on('click', '.ok-btn', function(event) {
                var row = $('#'+options.id).DataTable().row($(this).parents('tr')).data();
                labels[layer] = row;
                $(document).trigger('selected-'+options.id, {"labels":labels});
                layer = 0;
                fetchAndInit();
            });

            
            fetchAndInit();

            // Puedes exponer un método para refrescar columnas y datos si quieres:
            $table.data('refresh', fetchAndInit);
        });
    };
})(jQuery);