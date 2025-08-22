(function ($) {
    $.fn.addElementToTable = function (element) {
        return this.each(function () {
            const $table = $(this);
            var table = $table.DataTable();
            table.rows.add([element]);
            table.draw();
        });
    };
    $.fn.localSmartTable = function (options = {}) {
        
        const $table = $(this);
        
        var columns = [];
        for (let col in options.columns){
            let column = { data: col };
            columns.push(column);
        }
        console.log(columns);
        var table = $table.DataTable({
            columns: columns,
            data: [{
                "name": "test"
            }],
        });
    };
})(jQuery);