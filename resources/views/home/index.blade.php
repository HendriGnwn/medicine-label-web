@extends('layouts.admin')
@section('headerTitle', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Jumlah Pasien Per Poli Hari ini</div>

            <div class="panel-body">
                
                <table class="table table-condensed table-hover" style='margin-bottom: 0px'>
                    <thead>
                        <tr>
                            <th style="width:80%">Poli</th>
                            <th style="width:20%">Jumlah</th>
                        </tr>
                    </thead>
                </table>
                <div style='max-height: 800px; overflow-y: auto'>
                    <table class="table table-condensed table-hover">
                        <tbody id="patient-poly">
                            <tr>
                                <td colspan="2">Please wait...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <label class="label label-info">
                        {{ \Carbon\Carbon::now()->format('d M Y') }}
                    </label>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">Laporan Print Obat</div>
            <div class="panel-body">
                <canvas id="canvas"></canvas>
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
<!--</div>
<div class="row">-->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Daftar 10 User Terakhir Login</div>

            <div class="panel-body">
                <table class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Terakhir Login</th>
                        </tr>
                    </thead>
                    <tbody id="top-users">
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
                
                var topUsers = data.topUsers;
                $("#top-users").html("");
                if (topUsers.length > 0) {
                    for(var i=0; i<topUsers.length; i++) {
                        var itemName = topUsers[i].name;
                        $("#top-users").append("<tr><td>"+ itemName +"</td><td>"+ topUsers[i].last_login_at +"</td></tr>");
                    }
                } else {
                    $("#top-users").append("<tr><td colspan='2'>Tidak ada data</td></tr>");
                }
                
                var patientPoly = data.patientPoly;
                $("#patient-poly").html("");
                if (patientPoly.length > 0) {
                    for(var i=0; i<patientPoly.length; i++) {
                        var itemName = patientPoly[i].nama_unit;
                        $("#patient-poly").append("<tr><td>"+ itemName +"</td><td>"+ patientPoly[i].qty +"</td></tr>");
                    }
                } else {
                    $("#patient-poly").append("<tr><td colspan='2'>Tidak ada data</td></tr>");
                }
            }
        });
    }
</script>
<script>
		var barChartData = {
			labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
			datasets: [{
				label: 'Dataset 1',
				backgroundColor: 'rgb(255, 99, 132)',
				yAxisID: 'y-axis-1',
				data: [
					10,
					10,
					10,
					10,
					10,
					10,
					10
				]
			}, {
				label: 'Dataset 2',
				backgroundColor: 'rgb(255, 159, 64)',
				yAxisID: 'y-axis-2',
				data: [
					
					10,
					10,
					10,
					10,
					10,
					10,
					10
				]
			}]
		};
        window.onload = function() {
            $.getJSON("{{route('ajax.home-report-label')}}", function(result) {
                var ctx = document.getElementById('canvas').getContext('2d');
                window.myBar = new Chart(ctx, {
                    type: 'bar',
                    data: result,
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: 'Laproan Print Label'
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: true
                        },
                        scales: {
                            yAxes: [{
                                type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                display: true,
                                position: 'left',
                                id: 'y-axis-1',
                                scaleLabel:{
                                    display: true,
                                    labelString: 'Lembar'
                                }
                            }, {
                                type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                display: true,
                                position: 'right',
                                id: 'y-axis-2',
                                gridLines: {
                                    drawOnChartArea: false
                                },
                                scaleLabel:{
                                    display: true,
                                    labelString: 'Lembar'
                                }
                            }, {
                                type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                display: false,
                                position: 'left',
                                id: 'y-axis-3',
                            }, {
                                type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                display: false,
                                position: 'left',
                                id: 'y-axis-4',
                            }, {
                                type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                display: false,
                                position: 'left',
                                id: 'y-axis-5',
                            }],
                        }
                    }
                });
            });
		};
</script>
<!--<script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>-->
<script src="{{ asset('vendor/canvasjs-2.2/jquery.canvasjs.min.js') }}"></script>
<script src="{{ asset('js/chart.min.js') }}"></script>
@endpush