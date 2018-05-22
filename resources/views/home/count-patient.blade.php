@extends('layouts.admin')
@section('headerTitle', 'Daftar Pasien ' . $label)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Daftar Pasien <label class="label label-info">{{ $label }}</label></div>
            <div class="panel-body">
                <table id="patient-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>*</th>
                            <th>No Pendaftaran</th>
                            <th>No Antrian</th>
                            <th>No Rekam Medis - Pasien</th>
                            <th>Tanggal Pendaftaran</th>
                            <th>Dokter</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection


@push('script')
<script>
var oTable;
oTable = $('#patient-table').DataTable({
    processing: true,
    serverSide: true,
    dom: 'lBfrtip',
    order:  [[ 0, "asc" ]],
    pagingType: 'full_numbers',
    buttons: [
        {
            extend: 'print',
            autoPrint: true,
            customize: function ( win ) {
                $(win.document.body)
                    .css( 'padding', '2px' )
                    .prepend(
                        'Laporan Daftar Label<br/><font style="font-size:8px;margin-top:15px;">{{date('Y-m-d h:i:s')}}</font><br/><br/><br/>'
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
            text: '<i class="fa fa-print" data-toggle="tooltip" title="" data-original-title="Print"></i>'
        },
        {extend: 'colvis', text: '<i class="fa fa-eye" data-toggle="tooltip" title="" data-original-title="Column visible"></i>'},
        {extend: 'csv', text: '<i class="fa fa-file" data-toggle="tooltip" title="" data-original-title="Export CSV"></i>'},
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
    url: '{!! route('home.count-patient-data') !!}',
//        data: function (d) {
//            d.range = $('input[name=drange]').val();
//            d.created_range = $('input[name=created_range]').val();
//            d.updated_range = $('input[name=updated_range]').val();
//        }
    },
    columns: [
		{ data: "rownum", name: "rownum", searchable: false },
        { data: "no_pendaftaran", name: "no_pendaftaran" },
		{ data: "no_antrian", name: "no_antrian" },
		{ data: "no_rekam_medis", name: "no_rekam_medis" },
		{ data: "tanggal_pendaftaran", name: "tanggal_pendaftaran" },
		{ data: "id_dokter", name: "id_dokter" },
		{ data: "id_unit", name: "id_unit" },
    ],
});

$("#patient-table_wrapper > .dt-buttons").appendTo("div.export-options-container");


oTable.page.len(25).draw();

</script>
@endpush
