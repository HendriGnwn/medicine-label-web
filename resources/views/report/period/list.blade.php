@extends('layouts.admin')
@section('headerTitle', 'Laporan Obat Periode ' . app('request')->get('date_period'))

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        <a class="btn btn-success" href="{{ route('report.period.export-to-excel', ['date_period' => app('request')->get('date_period')]) }}" target="_blank">Export (xlsx)</a>
        <br/><br/>
        <div class="table-responsive">
            <table class="table table-lg table-bordered table-hover" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pasien</th>
                        <th>No RM</th>
                        <th>No Resep</th>
                        <th>No Pendaftaran</th>
                        <th>Nilai Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $no = 1;
                    $subTotal = 0;
                    @endphp
                    @foreach ($models as $model)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $model->mmPatientRegistration->mmPatient->nama }}</td>
                        <td>{{ $model->no_rekam_medis }}</td>
                        <td>{{ $model->no_resep }}</td>
                        <td>{{ $model->no_pendaftaran }}</td>
                        @php
                        $total = \App\Helpers\NumberFormatter::ceiling($model->getCalculatePriceTotal());
                        $subTotal += $total;
                        @endphp
                        <td><a target="_blank" href="{{route('report.period.list-detail', ['id_pendaftaran' => $model->id_pendaftaran, 'no_resep' => $model->no_resep])}}">{{ \App\Helpers\NumberFormatter::currencyIDR($total) }}</a></td>
                    </tr>
                    @endforeach
                </tbody>    
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align:right;">Total Keseluruhan</td>
                        <td>Rp. {{ \App\Helpers\NumberFormatter::currencyIDR($subTotal) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection