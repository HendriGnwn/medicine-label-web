@extends('layouts.admin')
@section('headerTitle', 'Laporan Obat per Jenis Transaksi ' . app('request')->get('date_period'))

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        <a class="btn btn-success" href="{{ route('report.transaction-type.export-to-excel', ['date_period' => app('request')->get('date_period'), 'transaction_type' => app('request')->get('transaction_type')]) }}" target="_blank">Export (xlsx)</a>
        <br/><br/>
        <div class="table-responsive">
            <table class="table table-lg table-bordered table-hover" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Jumlah</th>
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
                        <td>{{ $model->mmItem->nama_barang }}</td>
                        @php
                        $total = $model->medicine_qty;
                        $subTotal += $total;
                        @endphp
                        <td>{{ \App\Helpers\NumberFormatter::currencyIDR($total) }}</td>
                    </tr>
                    @endforeach
                </tbody>    
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align:right;">Total Keseluruhan</td>
                        <td>{{ \App\Helpers\NumberFormatter::currencyIDR($subTotal) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection