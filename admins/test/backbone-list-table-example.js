jQuery(document).ready(function($) {

    var tableData = new wp.table.collections.Rows();
    tableData.fetch();

    new wp.table.views.Table({
        columns: table_args.columns,
        collection: tableData,
        el: $('table.' + table_args.screen.base)
    }).render();


    $('#more-movies').on('click', function() {
        tableData.fetch();
    });
});