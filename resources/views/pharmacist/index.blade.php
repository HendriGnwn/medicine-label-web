@extends('layouts.admin')
@section('headerTitle', 'Daftar Apoteker')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        <input type="hidden" id="delete_value" name="delete_value"/>
        <div class="table-overflow">
            <table id="pharmacist-table" class="table table-lg table-hover" width="100%">
                <thead>
                    <tr>
                        <th style='width:5%'>*</th>
                        <th style='width:10%'>Nama Apoteker</th>
                        <th style='width:10%'>SIK</th>
                        <th style='width:10%'>Dibuat</th>
                        <th style='width:10%'>Diedit</th>
                        <th style='width:10%'></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="filter-search-dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="filter-search-form">
                <div class="modal-header">
                    <h4 style="margin-top:0px;margin-bottom:0px;">Filter Search<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div aria-required="true" class="form-group required form-group-default">
                                {!! Form::label('created_range', 'Tanggal Dibuat') !!}
                                {!! Form::text('created_range', null, ['class' => 'form-control date-range form-sm']) !!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div aria-required="true" class="form-group required form-group-default">
                                {!! Form::label('updated_range', 'Tanggal Diedit') !!}
                                {!! Form::text('updated_range', null, ['class' => 'form-control date-range form-sm']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="search" class="btn btn-primary btn-rounded">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
var oTable;
oTable = $('#pharmacist-table').DataTable({
    processing: true,
    serverSide: true,
    dom: 'lBfrtip',
    order:  [[ 1, "desc" ]],
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
        {
            text: '<i class="fa fa-plus"></i>&nbsp;&nbsp;Create',
            action: function ( e, dt, node, config ) {
                window.location = '{{ route("pharmacist.create") }}';
            }
        },
        {
            text: '<i class="fa fa-search"></i>&nbsp;&nbsp;Filter',
            action: function ( e, dt, node, config ) {
                $("#filter-search-dialog").modal({
                    show: true,
                    backdrop: 'static', 
                    keyboard: false
                });
            }
        }
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
    url: '{!! route('pharmacist.data') !!}',
        data: function (d) {
            d.range = $('input[name=drange]').val();
            d.created_range = $('input[name=created_range]').val();
            d.updated_range = $('input[name=updated_range]').val();
        }
    },
    columns: [
		{ data: "rownum", name: "rownum", searchable: false },
		{ data: "name", name: "name" },
		{ data: "sik", name: "sik" },
		{ data: "created_at", name: "created_at" },
		{ data: "updated_at", name: "updated_at", visible: false },
        { data: "action", name: "action", searchable: false, orderable: false },
    ],
});

$("#concept-table_wrapper > .dt-buttons").appendTo("div.export-options-container");

$('#filter-search-form').on('submit', function(e) {
    $("#filter-search-dialog").modal('hide');
    oTable.draw();
    e.preventDefault();
});

oTable.page.len(25).draw();

function deleteRecord(id){
    var confirm = window.confirm("Apakah Anda yakin akan menghapus data ini?");
    if (confirm == false) {
        return false;
    }
    $.ajax({
        url: '{{route("pharmacist.index")}}' + "/" + id + '?' + $.param({"_token" : '{{ csrf_token() }}' }),
        type: 'DELETE',
        complete: function(data) {
            oTable.draw();
        }
    });
}

$('.date-range').daterangepicker({
    autoUpdateInput: false
});
$('.date-range').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
});

$('.date-range').on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
});

</script>
@endpush
