function format(d){
        if(d.accountLevel == "2"){
            return '<div class="row">'+
            '<div class="col col-md-6">'+
            '<table>'+
            '<tr><td><b>Retired Status:</b> &nbsp;</td><td>'+d.retiredStatus+'</td></tr>'+
            '<tr><td><b>Warranty Experation:</b> &nbsp;</td><td>'+d.warrantyExp+'</td></tr>'+
            '<tr><td><b>Manufacturer:</b> &nbsp;</td><td>'+d.manufacturer+'</td></tr>'+
            '<tr><td><b>Price:</b> &nbsp;<td>$'+d.price+'</div>'+
            '</table></div><div class="col-md-6"><table>'+
            '<tr><td><b>Serial Number:</b> &nbsp;</td><td>'+d.serialNum+'</td></tr>'+
            '<tr><td><b>Description:</b> &nbsp;</td><td>'+d.description+'</td></tr>'+
            '</table></div>';
        }else{
            return '<div class="row">'+
            '<div class="col col-md-6">'+
            '<table>'+
            '<tr><td><b>Retired Status:</b> &nbsp;</td><td>'+d.retiredStatus+'</td></tr>'+
            '<tr><td><b>Warranty Experation:</b> &nbsp;</td><td>'+d.warrantyExp+'</td></tr>'+
            '<tr><td><b>Manufacturer:</b> &nbsp;</td><td>'+d.manufacturer+'</td></tr>'+
            '</table></div><div class="col-md-6"><table>'+
            '<tr><td><b>Price:</b> &nbsp;<td>$'+d.price+'</div>'+
            '<tr><td><b>Serial Number:</b> &nbsp;</td><td>'+d.serialNum+'</td></tr>'+
            '<tr><td><b>Description:</b> &nbsp;</td><td>'+d.description+'</td></tr>'+
            '</table></div>';
        }
}
$().ready(function(){
    var table = $('#assetTable').DataTable({
        "ajax": "txt/ajaxCompReport.txt",
        "columns":[
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            { "data": "type"},
            { "data": "computerName"},
            { "data": "location"},
            { "data": "currentUser"},
            { "data": "createdBy"},
        ],
        
    });
    $('#assetTable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
});