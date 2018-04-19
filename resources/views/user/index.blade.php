@extends('layouts.admin')
@section('headerTitle', 'Daftar User')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        @yield('headerTitle')
    </div>
    <div class="panel-body">
        <a href="{{ route('user.create') }}" class="btn btn-primary">Tambah User</a>
        <br/><br/>
        <input type="hidden" id="delete_value" name="delete_value"/>
        <div class="table-overflow">
            <table id="concept-table" class="table table-lg table-hover" width="100%">
                <thead>
                    <tr>
                        <th style='width:5%'>*</th>
                        <th style='width:20%'>Nama</th>
                        <th style='width:20%'>Username</th>
                        <th style='width:10%'>Role</th>
                        <th style='width:15%'>Dibuat</th>
                        <th style='width:15%'>Diedit</th>
                        <th style='width:8%'></th>
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
    url: '{!! route('user.data') !!}',
        data: function (d) {
            d.range = $('input[name=drange]').val();
        }
    },
    columns: [
		{ data: "rownum", name: "rownum", searchable: false },
		{ data: "name", name: "name" },
		{ data: "username", name: "username" },
		{ data: "role", name: "role" },
		{ data: "created_at", name: "created_at" },
		{ data: "updated_at", name: "updated_at", visible: false },
        { data: "action", name: "action", searchable: false, orderable: false },
    ],
    createdRow: function ( row, data, index ) {
        var createdAt = new Date(data['created_at']);
        var date = createdAt.getFullYear() + '-' + (createdAt.getMonth() + 1) + '-' + createdAt.getDate();
        var dateNow = new Date().getFullYear() + '-' + (new Date().getMonth() + 1) + '-' + new Date().getDate()
        
        if (date == dateNow) {
            $('td', row).parent().addClass('row-success');
        }
    },
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
        url: '{{route("user.index")}}' + "/" + id + '?' + $.param({"_token" : '{{ csrf_token() }}' }),
        type: 'DELETE',
        complete: function(data) {
            oTable.draw();
        }
    });
}

</script>
@endpush
