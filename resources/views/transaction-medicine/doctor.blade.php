@extends('layouts.admin')
@section('headerTitle', 'List Manually')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        <div class="table-overflow">
            <table id="medicine-table" class="table table-lg table-hover" width="100%">
                <thead>
                    <tr>
                        <th style='width:5%'>*</th>
                        <th style='width:10%'>No Registrasi</th>
                        <th style='width:10%'>No Resep</th>
                        <th style='width:30%'>No Medis Medis</th>
                        <th style='width:10%'>Tipe Rawat</th>
                        <th style='width:10%'>Dibuat</th>
                        <th style='width:10%'>Diedit</th>
                        <th style='width:10%'>Dibuat Oleh</th>
                        <th style='width:10%'>Diedit Oleh</th>
                        <th style='width:5%'></th>
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
oTable = $('#medicine-table').DataTable({
    processing: true,
    serverSide: true,
    dom: 'lBfrtip',
    order:  [[ 5, "desc" ]],
    pagingType: 'full_numbers',
    buttons: [
        {extend: 'colvis', text: '<i class="fa fa-eye"></i>'},
        {extend: 'csv', text: '<i class="fa fa-file" data-toggle="tooltip" title="" data-original-title="Export CSV"></i>'}
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
    url: '{!! route('transaction-medicine.doctor-data') !!}',
        data: function (d) {
            d.range = $('input[name=drange]').val();
        }
    },
    columns: [
		{ data: "rownum", name: "rownum", searchable: false },
		{ data: "registered_id", name: "registered_id" },
		{ data: "receipt_number", name: "receipt_number" },
		{ data: "medical_record_number", name: "medical_record_number" },
		{ data: "care_type", name: "care_type" },
		{ data: "created_at", name: "created_at" },
		{ data: "updated_at", name: "updated_at", "visible":false },
		{ data: "created_by", name: "created_by", "visible":false },
		{ data: "updated_by", name: "updated_by", "visible":false },
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

$('#formsearch').submit(function () {
    oTable.search( $('#search-table').val() ).draw();
    return false;
} );

oTable.page.len(25).draw();

</script>
@endpush