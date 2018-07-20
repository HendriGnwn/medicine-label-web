@extends('layouts.admin')
@section('headerTitle', 'Laporan Obat Berdasarkan Jenis Transaksi')


@section('content')
<div class="col-md-offset-2 col-md-8">
    <div class="panel panel-default">
        <div class="panel-heading">@yield('headerTitle')</div>

        <div class="panel-body">
            <form id="filter-search-form" action="">
                <div class="row">
                    <div class="col-md-4">
                        <div aria-required="true" class="form-group required form-group-default">
                            {!! Form::label('date_period', 'Periode') !!}
                            {!! Form::text('date_period', null, ['class' => 'form-control date-range form-sm', 'required' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div aria-required="true" class="form-group required form-group-default">
                            {!! Form::label('transaction_type', 'Jenis Transaksi') !!}
                            {!! Form::select('transaction_type', ["" => "Pilih"] + \App\MmTransactionAddMedicine::reportTransactionTypeLabels(), null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="margin-top:27px;"></div>
                        <button type="submit" name="search" class="btn btn-primary btn-rounded">Search</button>
                        <a onclick="exportToExcel()" class="btn btn-success" href="javascript:;;">Export (xlsx)</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
function exportToExcel() {
    window.open('{{ route("report.transaction-type.export-to-excel") }}?' + $("#filter-search-form").serialize(), '_blank');
    return false;
}

$("#filter-search-form").submit(function() {
    window.open('{{ route("report.transaction-type.list") }}?' + $("#filter-search-form").serialize(), '_blank');
    return false;
});
    
$('.date').datepicker({
    todayHighlight: true
});
$('.date-range').daterangepicker({
    autoUpdateInput: false,
    maxDate: moment().endOf("day")
});
$('.date-range').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
});

$('.date-range').on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
});

</script>
@endpush