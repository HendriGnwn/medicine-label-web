@extends('layouts.admin')
@section('headerTitle', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Grafik 10 Obat teratas 1 Bulan Terakhir</div>

            <div class="panel-body">
                <table class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>Obat</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody id="top-five-medicines">
                        <tr>
                            <td colspan="2">Please wait...</td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center">
                    <label class="label label-info">
                        {{ (new \Carbon\Carbon('first day of last month'))->format('d M Y')  }} - {{ (new \Carbon\Carbon('last day of last month'))->format('d M Y')  }}
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading">Jumlah Pasien Hari ini</div>

            <div class="panel-body text-center">
                <h1 id="count-patient-now">Please wait...</h1>
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
                <h1 id="count-patient-previous-month">Please wait...</h1>
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

@push('script')
<script>
    window.onload = function () {
        $.ajax({
            url: "{{ route('ajax.home-data') }}",
            type: 'GET',
            success: function (result) {
                console.log(result);
                if (result.status == 0) {
                    console.log('Tidak Ada data');
                    return false;
                }

                var data = result.data;

                $("#count-patient-now").html("<a href='{{ route("home.count-patient", ["category" => 1]) }}'>" + data.countPatientNow + "</a>");
                $("#count-patient-previous-month").html("<a href='{{ route("home.count-patient", ["category" => 2]) }}'>" + data.countPatientPreviousMonth + "</a>");
                
                var topFiveMedicines = data.topFiveMedicines;
                $("#top-five-medicines").html("");
                if (topFiveMedicines.length > 0) {
                    for(var i=0; i<topFiveMedicines.length; i++) {
                        var itemName = '';
                        if (topFiveMedicines[i].mm_item != null) {
                            itemName = topFiveMedicines[i].mm_item.nama_barang;
                        }
                        $("#top-five-medicines").append("<tr><td>"+ itemName +"</td><td>"+ topFiveMedicines[i].jml_permintaan +"</td></tr>");
                    }
                } else {
                    $("#top-five-medicines").append("<tr><td colspan='2'>Tidak ada data</td></tr>");
                }
            }
        });
    }
</script>
@endpush