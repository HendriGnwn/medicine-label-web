@extends('layouts.admin')
@section('headerTitle', 'List Manually')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
<!--        <form action="" method="post" id="form-search">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="medical_record" class="control-label">Nomor Rekam Medis</label>
                        <input id="medical_record" type="text" class="form-control" name="medical_record" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_id" class="control-label">Kode Pembayaran</label>
                        <input id="payment_id" type="text" class="form-control" name="payment_id" />
                    </div>
                </div>
                <div class="col-md-4">
                    <br/>
                    <button type="submit" name="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
        <br/><br/>-->
        
        <input type="hidden" id="delete_value" name="delete_value"/>
        <div class="table-overflow">
            <table id="concept-table" class="table table-lg table-hover" width="100%">
                <thead>
                    <tr>
                        <th style='width:5%'>*</th>
                        <th style='width:30%'>Nomor Medis Medis</th>
                        <th style='width:30%'>Dokter</th>
                        <th style='width:15%'>Tipe Rawat</th>
                        <th style='width:10%'>Dibuat</th>
                        <th style='width:15%'></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
var oTable;
oTable = $('#concept-table').DataTable({
    processing: true,
    serverSide: true,
    dom: 'lBfrtip',
    order:  [[ 4, "desc" ]],
    pagingType: 'full_numbers',
    buttons: [
        {
            extend: 'print',
            autoPrint: true,
            customize: function ( win ) {
                $(win.document.body)
                    .css( 'padding', '2px' )
                    .prepend(
                        '<img src="{{asset('img/logo.png')}}" style="float:right; top:0; left:0;height: 40px;right: 10px;background: #101010;padding: 8px;border-radius: 4px" /><h5 style="font-size: 9px;margin-top: 0px;"><br/><font style="font-size:14px;margin-top: 5px;margin-bottom:20px;"> Report Concept</font><br/><br/><font style="font-size:8px;margin-top:15px;">{{date('Y-m-d h:i:s')}}</font></h5><br/><br/>'
                    );


                $(win.document.body).find( 'div' )
                    .css( {'padding': '2px', 'text-align': 'center', 'margin-top': '-50px'} )
                    .prepend(
                        ''
                    );

                $(win.document.body).find( 'table' )
                    .addClass( 'compact' )
                    .css( { 'font-size': '9px', 'padding': '2px' } );


            },
            title: '',
            orientation: 'landscape',
            exportOptions: {columns: ':visible'} ,
            text: '<i class="fa fa-print" data-toggle="tooltip" title="" data-original-title="Print"></i>',
            //className: 'btn btn-primary'
        },
        {extend: 'colvis', text: '<i class="fa fa-eye" data-toggle="tooltip" title="" data-original-title="Column visible"></i>'},
        {extend: 'csv', text: '<i class="fa fa-file-excel-o" data-toggle="tooltip" title="" data-original-title="Export CSV"></i>'}
    ],
    //sDom: "<'table-responsive fixed't><'row'<p i>> B",
    sPaginationType: "bootstrap",
    destroy: true,
    responsive: true,
    scrollCollapse: true,
    oLanguage: {
        "sLengthMenu": "_MENU_ ",
        "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
    },
    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
    ajax: {
    url: '{!! route('manually.data') !!}',
        data: function (d) {
            d.range = $('input[name=drange]').val();
        }
    },
    columns: [
		{ data: "rownum", name: "rownum" },
		{ data: "medical_record_number", name: "medical_record_number" },
		{ data: "doctor_id", name: "doctor_id" },
		{ data: "care_type", name: "care_type" },
		{ data: "created_at", name: "created_at" },
        { data: "action", name: "action", searchable: false, orderable: false },
    ],
});

$("#concept-table_wrapper > .dt-buttons").appendTo("div.export-options-container");

$('#formsearch').submit(function () {
    oTable.search( $('#search-table').val() ).draw();
    return false;
} );

oTable.page.len(25).draw();

function deleteRecord(id){
    var confirm = window.confirm("Apakah Anda yakin akan menghapus data ini?");
    if (confirm == false) {
        return false;
    }
    $.ajax({
        url: '{{route("manually.index")}}' + "/" + id + '?' + $.param({"_token" : '{{ csrf_token() }}' }),
        type: 'DELETE',
        complete: function(data) {
            oTable.draw();
        }
    });
}

</script>
@endpush