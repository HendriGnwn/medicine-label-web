@extends('layouts.admin')
@section('headerTitle', 'Detail Laporan Obat #' . $model->no_pendaftaran)

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        <table class="table table-bordered table-condensed">
            <tr>
                <td>No Pendaftaran</td>
                <td>{{ $model->no_pendaftaran }}</td>
            </tr>
            <tr>
                <td>No RM</td>
                <td>{{ $model->no_rekam_medis }}</td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
                <td>{{ $model->mmPatient->nama }}</td>
            </tr>
            <tr>
                <td>No Resep</td>
                <td>{{ app('request')->get('no_resep') }}</td>
            </tr>
        </table>
        <div class="table-responsive">
            <table class="table table-lg table-bordered table-hover" width="100%">
                <thead>
                    <tr>
                        <th>*</th>
                        <th>Obat</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $no = 1;
                    $subTotal = 0;
                    @endphp
                    @foreach ($medicines as $medicine)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $medicine->mmItem->nama_barang }}</td>
                        <td>{{ $medicine->jml_permintaan }}</td>
                        <td>Rp. {{ \App\Helpers\NumberFormatter::currencyIDR($medicine->harga) }}</td>
                        @php
                        $total = \App\Helpers\NumberFormatter::ceiling($medicine->jml_permintaan * $medicine->harga);
                        $subTotal += $total;
                        @endphp
                        <td>Rp. {{ \App\Helpers\NumberFormatter::currencyIDR($total) }}</td>
                    </tr>
                    @endforeach
                </tbody>    
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;">Total Keseluruhan</td>
                        <td>Rp. {{ \App\Helpers\NumberFormatter::currencyIDR($subTotal) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection