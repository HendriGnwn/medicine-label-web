@extends('layouts.admin')
@section('headerTitle', 'Laporan Harian Obat ' . app('request')->get('date_period'))

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        <a class="btn btn-success" href="{{ route('report.export-to-excel', ['date_period' => app('request')->get('date_period')]) }}" target="_blank">Export (xlsx)</a>
        <br/><br/>
        <div class="table-responsive">
            <table class="table table-lg table-bordered table-hover" width="100%">
                <thead>
                    <tr>
                        <th>*</th>
                        <th>Pasien</th>
                        <th>No Resep</th>
                        @foreach ($medicines as $medicine)
                        <th style="width:5%;">{{ $medicine->mmItem->nama_barang }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                    $no = 1;
                    @endphp
                    @foreach ($models as $model)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $model->mmPatient->nama }}</td>
                        <td>{{ $model->no_resep }}</td>
                        @foreach ($medicines as $medicine)
                        <td style="text-align:center;">{{ $model->getItemQuantity($medicine->id_barang, $model->no_resep) }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>    
                <tfoot>
                    <tr>
                        <td colspan="3">Jumlah Keseluruhan</td>
                        @foreach ($medicines as $medicine)
                        <td style="text-align:center;">{{ $medicine->total_jml_permintaan }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$('.date').datepicker();
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