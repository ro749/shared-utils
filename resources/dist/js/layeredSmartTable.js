(function ($) {
    $.fn.layeredSmartTable = function (options = {}) {
        return this.each(function () {
            const $table = $(this);
            let layer = 0;
            let selected_layers = [];
            var labels = [];
            function initTable(metadata) {
                $('#'+options.id).DataTable().destroy();
                $table.empty();
                
                create_html(metadata);
                metadata.layer = layer;
                metadata.manual_filters = selected_layers;
                $table.smartTable(metadata);
            }

            function fetchAndInit() {
                $.ajax({
                    url: '/table/' + options.id + '/metadata',
                    type: 'GET',
                    dataType: 'json',
                    data:
                    {
                        layer: layer,
                        selected_id: layer > 0 ? selected_layers[layer-1] : 0 
                    },
                    success: function (metadata) {
                        initTable(metadata);
                    }
                });
            }

            function create_html(metadata){
                $('#'+options.id+'-container .title').html(metadata.title);
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
                $('#' + options.id + '-container .back-btn').css('visibility', 'visible');
                fetchAndInit();
            });

            $table.on('click', '.ok-btn', function(event) {
                var row = $('#'+options.id).DataTable().row($(this).parents('tr')).data();
                labels[layer] = row;
                $(document).trigger('selected-'+options.id, {"labels":labels});
                layer = 0;
                $table.empty();
                $('#'+options.id+'-container .title').html('');
                $('#'+options.id).html('');
            });

            $('#'+options.id+'-container').on('click','.back-btn', function(event) {
                layer -= 1;
                if(layer == 0){
                    $('#' + options.id + '-container .back-btn').css('visibility', 'hidden');
                }
                fetchAndInit();
            });

            if($table.is(':visible')){
                fetchAndInit();
            }
            
            $table.data('fetchAndInit', fetchAndInit);
        });
    };

    $.fn.refreshLayeredSmartTable = function () {
        return this.each(function () {
            const $table = $(this);
            if ($table.data('fetchAndInit')) {
                $table.data('fetchAndInit')();
            } else {
                console.warn("No fetchAndInit method found for this table.");
            }
        });
    };

})(jQuery);