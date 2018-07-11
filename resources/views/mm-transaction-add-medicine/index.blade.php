@extends('layouts.admin')
@section('headerTitle', 'Daftar dari SIMRS')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        <div class="table-overflow">
            <table id="medicine-table" class="table table-lg table-hover" width="100%">
                <thead>
                    <tr>
                        <th style='width:5%'>*</th>
                        <th style='width:12%'>No Pendaftaran</th>
                        <th style='width:10%'>No Resep</th>
                        <th style='width:25%'>No Rekam Medis - Pasien</th>
                        <th style='width:25%'>Unit - Dokter</th>
                        <th style='width:5%'>Print</th>
                        <th style='width:10%'>Tipe Rawatan</th>
                        <th style='width:12%'>Dibuat</th>
                        <th style='width:8%'>Dibuat Oleh</th>
                        <th style='width:12%'></th>
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
                                {!! Form::label('registration_number', 'Nomor Pendaftaran') !!}
                                {!! Form::text('registration_number', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div aria-required="true" class="form-group required form-group-default">
                                {!! Form::label('medical_record_number', 'Pasien') !!}
                                {!! Form::select('medical_record_number', [], null, ['class' => 'form-control', 'style'=>'width:100%']) !!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div aria-required="true" class="form-group required form-group-default">
                                {!! Form::label('doctor_id', 'Dokter') !!}
                                {!! Form::select('doctor_id', [], null, ['class' => 'form-control', 'style'=>'width:100%']) !!}
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
oTable = $('#medicine-table').DataTable({
    processing: true,
    serverSide: true,
    dom: 'lBfrtip',
    order:  [[ 7, "desc" ]],
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
        {extend: 'colvis', text: '<i class="fa fa-eye"></i>'},
        {extend: 'csv', text: '<i class="fa fa-file" data-toggle="tooltip" title="" data-original-title="Export CSV"></i>'},
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
    url: '{!! route('transaction-add-medicine.list-index') !!}',
        data: function (d) {
            d.range = $('input[name=drange]').val();
            d.registration_number = $('input[name=registration_number]').val();
            d.medical_record_number = $('select[name=medical_record_number] option:selected').val();
            d.doctor_id = $('select[name=doctor_id] option:selected').val();
        }
    },
    columns: [
		{data: 'DT_Row_Index', orderable: false, searchable: false},
		{ data: "id_pendaftaran", name: "id_pendaftaran" },
        { data: "no_resep", name: "no_resep" },
		{ data: "no_rekam_medis", name: "no_rekam_medis" },
        { data: "id_dokter", name: "id_dokter" },
		{ data: "print_count", name: "print_count", searchable: false, orderable: false },
        { data: "tipe_rawatan", name: "tipe_rawatan", visible: false },
		{ data: "created_date", name: "created_date" },
		{ data: "created_by", name: "created_by", visible: false },
        { data: "action", name: "action", searchable: false, orderable: false },
    ],
    createdRow: function ( row, data, index ) {
        if (data['print_count'] != '0') {
            $('td', row).parent().addClass('row-success');
        }
    },
    'columnDefs': [
        {
            "targets": 5,
            "className": "text-center"
       }
    ],
});

$('#filter-search-form').on('submit', function(e) {
    $("#filter-search-dialog").modal('hide');
    oTable.draw();
    e.preventDefault();
});

oTable.page.len(10).draw();

$('.date-range').daterangepicker({
    autoUpdateInput: false
});
$('.date-range').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
});

$('.date-range').on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
});
$('#doctor_id').select2({
    placeholder: "NIP - Nama Dokter",
    allowClear: true,
    width: 'resolve',
    minimumInputLength: 2,
    ajax: {
        url: "{{ route('doctor.find') }}",
        dataType: 'json',
        data: function (params) {
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    }
});

$('#medical_record_number').select2({
    placeholder: "No Rekam Medis - Nama Pasien",
    minimumInputLength: 2,
    width: 'resolve',
    allowClear: true,
    ajax: {
        url: "{{ route('patient.find') }}",
        dataType: 'json',
        data: function (params) {
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    }
});

</script>
@endpush