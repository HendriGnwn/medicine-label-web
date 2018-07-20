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
//        $startDate = Carbon::now()->subMonth(6)->format('Y-m');
//        $endDate = Carbon::now()->format('Y-m');
//        $dateRanges = \App\Helpers\DateTimeHelper::getArrayDateRange($startDate, $endDate);
//        
//        $ranaps = MmTransactionAddMedicine::select([
//                \DB::raw('COUNT(mm_transaksi_add_obat.id_transaksi_obat) as count'), \DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m") as date')
//            ])
//            ->join('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
//            ->whereRaw('(mm_pasien_pendaftaran.kelas_perawatan != 0 OR mm_pasien_pendaftaran.kelas_perawatan is not null)')
//            ->whereBetween(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'), [Carbon::now()->subMonth(7)->format('Y-m'), Carbon::now()->format('Y-m')])
//            ->groupBy(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'))
//            ->pluck('count', 'date');
//        
//        $rajalJkns = MmTransactionAddMedicine::select([
//                \DB::raw('COUNT(mm_transaksi_add_obat.id_transaksi_obat) as count'), \DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m") as date')
//            ])
//            ->join('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
//            ->whereRaw('((mm_pasien_pendaftaran.id_jenis_pembayaran IN (8,9)) AND (mm_pasien_pendaftaran.kelas_perawatan = 0 OR mm_pasien_pendaftaran.kelas_perawatan is null))')
//            ->whereBetween(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'), [Carbon::now()->subMonth(7)->format('Y-m'), Carbon::now()->format('Y-m')])
//            ->groupBy(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'))
//            ->pluck('count', 'date');
//        
//        $rajalRegulars = MmTransactionAddMedicine::select([
//                \DB::raw('COUNT(mm_transaksi_add_obat.id_transaksi_obat) as count'), \DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m") as date')
//            ])
//            ->join('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
//            ->whereRaw('((mm_pasien_pendaftaran.id_jenis_pembayaran = 0) AND (mm_pasien_pendaftaran.kelas_perawatan = 0 OR mm_pasien_pendaftaran.kelas_perawatan is null))')
//            ->whereBetween(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'), [Carbon::now()->subMonth(7)->format('Y-m'), Carbon::now()->format('Y-m')])
//            ->groupBy(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'))
//            ->pluck('count', 'date');
//        
//        $rajalCompanies = MmTransactionAddMedicine::select([
//                \DB::raw('COUNT(mm_transaksi_add_obat.id_transaksi_obat) as count'), \DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m") as date')
//            ])
//            ->join('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
//            ->whereRaw('((mm_pasien_pendaftaran.id_jenis_pembayaran = 4) AND (mm_pasien_pendaftaran.kelas_perawatan = 0 OR mm_pasien_pendaftaran.kelas_perawatan is null))')
//            ->whereBetween(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'), [Carbon::now()->subMonth(7)->format('Y-m'), Carbon::now()->format('Y-m')])
//            ->groupBy(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'))
//            ->pluck('count', 'date');
//        
//        $rajalIgds = MmTransactionAddMedicine::select([
//                \DB::raw('COUNT(mm_transaksi_add_obat.id_transaksi_obat) as count'), \DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m") as date')
//            ])
//            ->join('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
//            ->where('mm_pasien_pendaftaran.id_pendaftaran', 4)
//            ->whereBetween(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'), [Carbon::now()->subMonth(7)->format('Y-m'), Carbon::now()->format('Y-m')])
//            ->groupBy(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'))
//            ->pluck('count', 'date');
//        
//        $result0 = [$dateRanges[0]];
//        $result1 = [$dateRanges[1]];
//        $result2 = [$dateRanges[2]];
//        $result3 = [$dateRanges[3]];
//        $result4 = [$dateRanges[4]];
//        $result5 = [$dateRanges[5]];
//        $result6 = [$dateRanges[6]];
//        
//        for($i=0;$i<count($dateRanges);$i++) {
//            ${"result$i"}[] = array_key_exists($dateRanges[$i], $ranaps->toArray()) ? $ranaps[$dateRanges[$i]] : 0;
//            ${"result$i"}[] = array_key_exists($dateRanges[$i], $rajalJkns->toArray()) ? $rajalJkns[$dateRanges[$i]] : 0;
//            ${"result$i"}[] = array_key_exists($dateRanges[$i], $rajalRegulars->toArray()) ? $rajalRegulars[$dateRanges[$i]] : 0;
//            ${"result$i"}[] = array_key_exists($dateRanges[$i], $rajalCompanies->toArray()) ? $rajalCompanies[$dateRanges[$i]] : 0;
//            ${"result$i"}[] = array_key_exists($dateRanges[$i], $rajalIgds->toArray()) ? $rajalIgds[$dateRanges[$i]] : 0;
//        }
//        
//        $lava = new Lavacharts; // See note below for Laravel
//        $finances = $lava->DataTable();
//        $finances->addDateColumn('Year')
//                 ->addNumberColumn('RANAP')
//                 ->addNumberColumn('RAJAL JKN')
//                 ->addNumberColumn('RAJAL REGULER')
//                 ->addNumberColumn('RAJAL PERUSAHAAN')
//                 //->addNumberColumn('RAJAL PEGAWAI')
//                 ->addNumberColumn('IGD')
//                 ->setDateTimeFormat('Y-m')
//                 ->addRow($result0)
//                ->addRow($result1)
//                ->addRow($result2)
//                ->addRow($result3)
//                ->addRow($result4)
//                ->addRow($result5)
//                ->addRow($result6);
//
//        $lava->ColumnChart('ReportPrintLabel', $finances, [
//            'title' => 'Laporan Print Label',
//            'titleTextStyle' => [
//                'color'    => '#0163a7',
//                'fontSize' => 16
//            ],
//            'vAxis'=> ['title'=>'Lembar']
//        ]);

        return view('home.index');
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


/** RANAP
        SELECT COUNT(obat.id_transaksi_obat), DATE_FORMAT(obat.created_date, '%Y-%m') FROM mm_transaksi_add_obat obat
        JOIN mm_pasien_pendaftaran daftar ON daftar.id_pendaftaran = obat.id_pendaftaran
        WHERE 
        (daftar.kelas_perawatan != 0 OR daftar.kelas_perawatan is not null) 
        AND 
        (DATE_FORMAT(obat.created_date, '%Y-%m') BETWEEN '2018-01' AND '2018-05')
        GROUP BY DATE_FORMAT(obat.created_date, '%Y-%m')
        */
        
        /** RAJAL JKN
        SELECT COUNT(obat.id_transaksi_obat), DATE_FORMAT(obat.created_date, '%Y-%m') FROM mm_transaksi_add_obat obat
        JOIN mm_pasien_pendaftaran daftar ON daftar.id_pendaftaran = obat.id_pendaftaran
        WHERE 
        ((daftar.id_jenis_pembayaran IN (8,9)) AND (daftar.kelas_perawatan = 0 OR daftar.kelas_perawatan is null)) AND 
        (DATE_FORMAT(obat.created_date, '%Y-%m') BETWEEN '2018-01' AND '2018-07')
        GROUP BY DATE_FORMAT(obat.created_date, '%Y-%m') 
        */
        
        /** RAJAL REGULER
        SELECT COUNT(obat.id_transaksi_obat), DATE_FORMAT(obat.created_date, '%Y-%m') FROM mm_transaksi_add_obat obat
        JOIN mm_pasien_pendaftaran daftar ON daftar.id_pendaftaran = obat.id_pendaftaran
        WHERE 
        ((daftar.id_jenis_pembayaran = 0) AND (daftar.kelas_perawatan = 0 OR daftar.kelas_perawatan is null)) AND 
        (DATE_FORMAT(obat.created_date, '%Y-%m') BETWEEN '2018-01' AND '2018-07')
        GROUP BY DATE_FORMAT(obat.created_date, '%Y-%m')
        */
        
        /** RAJAL PERUSAHAAN
        SELECT COUNT(obat.id_transaksi_obat), DATE_FORMAT(obat.created_date, '%Y-%m') FROM mm_transaksi_add_obat obat
        JOIN mm_pasien_pendaftaran daftar ON daftar.id_pendaftaran = obat.id_pendaftaran
        WHERE 
        ((daftar.id_jenis_pembayaran = 4) AND (daftar.kelas_perawatan = 0 OR daftar.kelas_perawatan is null)) AND 
        (DATE_FORMAT(obat.created_date, '%Y-%m') BETWEEN '2018-01' AND '2018-07')
        GROUP BY DATE_FORMAT(obat.created_date, '%Y-%m')
         */
        
        /** RAJAL IGD
        SELECT COUNT(obat.id_transaksi_obat), DATE_FORMAT(obat.created_date, '%Y-%m') FROM mm_transaksi_add_obat obat
        JOIN mm_pasien_pendaftaran daftar ON daftar.id_pendaftaran = obat.id_pendaftaran
        WHERE 
        (daftar.jenis_pendaftaran = 4) AND 
        (DATE_FORMAT(obat.created_date, '%Y-%m') BETWEEN '2018-01' AND '2018-07')
        GROUP BY DATE_FORMAT(obat.created_date, '%Y-%m')
         */

/** RAJAL PEGAWAI // <skip dlu>
 * 
 */