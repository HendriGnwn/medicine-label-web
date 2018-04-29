@extends('layouts.admin')
@section('headerTitle', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Grafik 10 Obat teratas</div>

            <div class="panel-body">
                <table class="table table-condensed table-hover">
                    <tr>
                        <th>Obat</th>
                        <th>Jumlah</th>
                    </tr>
                    @foreach ($topFiveMedicines as $medicine)
                    <tr>
                        <td>{{ $medicine->mmItem ? $medicine->mmItem->nama_barang : $medicine->name }}</td>
                        <td>{{ $medicine->quantity }}</td>
                    </tr>
                    @endforeach
                </table>
                <div class="text-center">
                    <label class="label label-info">
                        Semua waktu
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading">Jumlah Pasien Hari ini</div>

            <div class="panel-body text-center">
                <h1>{{ $countPatientNow }}</h1>
                <label class="label label-info">
                    {{ \Carbon\Carbon::now()->format('d M Y') }}
                </label>
                <br/>
                <br/>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading">Jumlah Pasien 1 Bulan Terakhir</div>

            <div class="panel-body text-center">
                <h1>{{ $countPatientPreviousMonth }}</h1>
                <label class="label label-info">
                    {{ (new \Carbon\Carbon('first day of last month'))->format('d M Y')  }} - {{ (new \Carbon\Carbon('last day of last month'))->format('d M Y')  }}
                </label>
                <br/>
                <br/>
            </div>
        </div>
    </div>
</div>

@endsection
