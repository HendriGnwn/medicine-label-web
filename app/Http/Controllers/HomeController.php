<?php

namespace App\Http\Controllers;

use App\MmPatientRegistration;
use App\MmTransactionAddMedicine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Khill\Lavacharts\Lavacharts;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $lava = new Lavacharts; // See note below for Laravel

        $finances = $lava->DataTable();
        $finances->addDateColumn('Year')
                 ->addNumberColumn('RANAP')
                 ->addNumberColumn('RAJAL JKN')
                 ->addNumberColumn('RAJAL REGULER')
                 ->addNumberColumn('RAJAL PERUSAHAAN')
                 ->addNumberColumn('RAJAL PEGAWAI')
                 ->addNumberColumn('IGD')
                 ->setDateTimeFormat('m')
                 ->addRow(['01', 1000, 400, 1000, 400, 1000, 400])
                 ->addRow(['02', 1170, 460, 1170, 460, 1170, 460])
                 ->addRow(['03', 660, 1120, 660, 1120, 660, 1120])
                 ->addRow(['04', 1030, 54, 1030, 54, 1030, 54]);

        $lava->ColumnChart('ReportPrintLabel', $finances, [
            'title' => 'Laporan Print Label',
            'titleTextStyle' => [
                'color'    => '#0163a7',
                'fontSize' => 16
            ],
            'vAxis'=> ['title'=>'Lembar']
        ]);
//        $votes->addStringColumn('Laporan Print Label')
//              ->addNumberColumn('Lembar')
//              ->addRow(['RANAP',  rand(1000,5000)])
//              ->addRow(['RAJAL JKN',  rand(1000,5000)])
//              ->addRow(['RAJAL REGULER',  rand(1000,5000)])
//              ->addRow(['RAJAL PERUSAHAAN', rand(1000,5000)])
//              ->addRow(['RAJAL PEGAWAI',   rand(1000,5000)])
//              ->addRow(['IGD', rand(1000,5000)]);

        //$lava->BarChart('ReportPrintLabel', $votes);

        return view('home.index', compact('lava'));
    }
    
    public function triggerDropAllOnBigData()
    {
        MmTransactionAddMedicine::deleteAllRecordOnBigData();
        
        echo 'success';
    }
    
    public function countPatient(Request $request)
    {
        $category = $request->get('category', 1);
        switch ($category) {
            case 1:
                $label = Carbon::now()->format('d M Y');
                break;
            case 2:
                $start = (new Carbon('first day of last month'))->format('d M Y');
                $end = (new Carbon('last day of last month'))->format('d M Y');
                $label = $start . ' - ' . $end;
                break;
        }
        
        return view('home.count-patient', compact('label'));
    }
    
    public function countPatientData(Request $request)
    {
        \DB::statement(DB::raw('set @rownum=0'));
        $category = $request->get('category', 1);
        $date = $request->get('date', Carbon::now()->toDateString());
        switch ($category) {
            case 1:
                $model = MmPatientRegistration::select([
                        DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'mm_pasien_pendaftaran.*'
                    ])
                    ->whereRaw("DATE_FORMAT(tanggal_pendaftaran, '%Y-%m-%d') = '". $date ."'");
                break;
            case 2:
                $start = (new Carbon('first day of last month'))->toDateString();
                $end = (new Carbon('last day of last month'))->toDateString();
                $model = MmPatientRegistration::select([
                        DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'mm_pasien_pendaftaran.*'
                    ])
                    ->whereRaw("DATE_FORMAT(tanggal_pendaftaran, '%Y-%m-%d') BETWEEN '{$start}' AND '{$end}'");
                break;
        }

         $datatables = app('datatables')->of($model)
            ->editColumn('no_rekam_medis', function ($model) {
                $patientName = $model->mmPatient ? $model->mmPatient->nama : '';
                return $model->no_rekam_medis . ' - ' . $patientName;
            })
            ->editColumn('id_dokter', function ($model) {
                return $model->mmDoctor ? $model->mmDoctor->nama_dokter : '';
            })
            ->editColumn('id_unit', function ($model) {
                return $model->mmUnit ? $model->mmUnit->nama_unit : '';
            });

        if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
        }

        return $datatables->make(true);
    }
}
