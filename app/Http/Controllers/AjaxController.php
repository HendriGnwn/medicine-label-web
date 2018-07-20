<?php

namespace App\Http\Controllers;

use App\MmDoctor;
use App\MmHowToUse;
use App\MmItem;
use App\MmPatient;
use App\MmPatientRegistration;
use App\MmTransactionAddMedicine;
use App\TransactionMedicine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Khill\Lavacharts\Lavacharts;

class AjaxController extends Controller
{
    /**
     * @param Request $request
     * @return type
     */
	public function findDoctor(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return response()->json([]);
        }
        
        $doctors = MmDoctor::where('nama_dokter', 'like', "%$term%")
                ->orWhere('nip', 'like', "%$term%")
                ->limit(20)
                ->groupBy('nama_dokter')
                ->get();
        $results = [];
        foreach ($doctors as $doctor) {
            $results[] = [
                'id' => $doctor->id_dokter,
                'text' => $doctor->nip . ' - ' . $doctor->nama_dokter,
            ];
        }
        
        return response()->json($results);
    }
    
    /**
     * @param Request $request
     * @return type
     */
	public function findDoctorAndUnit(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return response()->json([]);
        }
        
        $doctors = MmDoctor::where('nama_dokter', 'like', "%$term%")
                ->orWhere('id_unit', 'like', "%$term%")
                ->limit(20)
                ->groupBy('id_dokter')
                ->get();
        $results = [];
        foreach ($doctors as $doctor) {
            $results[] = [
                'id' => $doctor->id_dokter,
                'text' => $doctor->id_unit . ' - ' . $doctor->nama_dokter,
            ];
        }
        
        return response()->json($results);
    }
    
    /**
     * @param Request $request
     * @return type
     */
	public function findMedicine(Request $request)
    {
        $term = trim($request->q);
        
        if (empty($term)) {
            return response()->json([]);
        }
        
        $excepts = [];
        if (isset($request->except) && count($request->except) > 0) {
            foreach ($request->except as $except) {
                $excepts[] = (int)$except['value'];
            }
        }
        $items = MmItem::where('nama_barang', 'like', "%$term%")
            ->orWhere('kode_barang', 'like', "%$term%")
            ->where('id_barang_group', "1")
            ->where('id_barang_group', 1) //obat
            ->limit(20)
            ->get();
        
        $results = [];
        foreach ($items as $item) {
            if (in_array($item->id_barang, $excepts)) {
                $results[] = [
                    'id' => $item->id_barang,
                    'text' => $item->kode_barang . ' - ' . $item->nama_barang . ' (sudah diset)',
                    'disabled' => true,
                ];
            } else if ($item->stok <= 0) {
                $results[] = [
                    'id' => $item->id_barang,
                    'text' => $item->kode_barang . ' - ' . $item->nama_barang . ' (habis)',
                    'disabled' => true,
                ];
            } else {
                $results[] = [
                    'id' => $item->id_barang,
                    'text' => $item->kode_barang . ' - ' . $item->nama_barang,
                ];
            }
            
        }
        
        return response()->json($results);
    }
    
    /**
     * @param Request $request
     * @return type
     */
	public function findPatient(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return response()->json([]);
        }
        
        $patients = MmPatient::where('nama', 'like', "%$term%")
                ->orWhere('no_rekam_medis', 'like', "%$term%")
                ->limit(20)
                ->get();
        $results = [];
        foreach ($patients as $patient) {
            $results[] = [
                'id' => $patient->no_rekam_medis,
                'text' => $patient->no_rekam_medis . ' - ' . $patient->nama,
            ];
        }
        
        return response()->json($results);
    }
    
    /**
     * @param Request $request
     * @return type
     */
	public function findPatientRegistered(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return response()->json([]);
        }
        
        $patients = MmPatientRegistration::whereRaw('DATE_FORMAT(tanggal_pendaftaran, "%Y") >= "' . Carbon::now()->format('Y') . '"')
                ->whereHas('mmPatient', function ($query) use ($term) {
                    $query->where('nama', 'like', "%$term%");
                })
                ->orWhere('no_rekam_medis', 'like', "%$term%")
                //->orWhere('no_pendaftaran', 'like', "%$term%")
                
                ->whereRaw('(id_dokter != "0" OR id_dokter IS NOT NULL)')
                ->orderBy('tanggal_pendaftaran', 'desc')
                ->limit(20)
                ->get();
        $results = [];
        foreach ($patients as $patient) {
            $results[] = [
                'id' => $patient->id_pendaftaran,
                'text' => $patient->no_rekam_medis . ' - ' . $patient->mmPatient->nama,
            ];
        }
        
        return response()->json($results);
    }
    
    public function getResultPatientRegistered(Request $request)
    {
        $patientRegistered = MmPatientRegistration::find($request->registered_id);
        if (!$patientRegistered) {
            return response()->json([
                'status' => 0,
                'data' => null,
                'message' => 'Maaf, data tidak ditemukan',
            ]);
        }
        
        $careTypeId = ($patientRegistered->kelas_perawatan == 0) ? TransactionMedicine::CARE_TYPE_OUTPATIENT : TransactionMedicine::CARE_TYPE_INPATIENT;
        
        $isRegisterIgd = ($patientRegistered->jenis_pendaftaran == MmPatientRegistration::REGISTER_TYPE_IGD) ? true : false;
        $isRanap = ($patientRegistered->kelas_perawatan != 0) ? true : false;
        $receiptNumber = TransactionMedicine::generateReceiptNumber($patientRegistered->id_jenis_pembayaran, $isRegisterIgd, $isRanap);
        
        return response()->json([
            'status' => 1,
            'data' => [
                'registered_number' => $patientRegistered->no_pendaftaran,
                'registered_at' => $patientRegistered->tanggal_pendaftaran,
                'patient' => $patientRegistered->no_rekam_medis . ' - ' . $patientRegistered->mmPatient ? $patientRegistered->mmPatient->nama : 'tidak diketahui',
                'doctor' => $patientRegistered->mmDoctor ? $patientRegistered->mmDoctor->nama_dokter : 'belum di set',
                'doctor_select2' => $patientRegistered->mmDoctor ? $patientRegistered->mmDoctor->nip . ' - ' . $patientRegistered->mmDoctor->nama_dokter : 'belum di set',
                'unit' => $patientRegistered->mmUnit ? $patientRegistered->mmUnit->nama_unit : "tidak diset",
                'medical_record_number' => $patientRegistered->no_rekam_medis,
                'care_type_id' => $careTypeId,
                'doctor_id' => $patientRegistered->id_dokter,
                'unit_id' => $patientRegistered->id_unit,
                'receipt_number' => $receiptNumber,
            ]
        ]);
    }
    
    
    
    /**
     * @param Request $request
     * @return type
     */
	public function findMedicineHowToUse(Request $request)
    {
        $term = trim($request->term);
        if (empty($term)) {
            return response()->json([]);
        }
        
        $medicineId = trim($request->medicine_id, 0);
        $item = MmItem::where('id_barang', $medicineId)->first();
        if (!$item) {
            $howToUses = MmHowToUse::where('nama', 'like', "%$term%")->get();
        } else {
            $howToUses = MmHowToUse::where('nama', 'like', "%$term%")
                    ->where('id_barang_satuan_kecil', $item->id_barang_satuan_kecil)
                    ->get();
        }
        
        $results = [];
        foreach ($howToUses as $howToUse) {
            $results[] = [
                'id' => $howToUse->nama,
                'value' => $howToUse->nama
            ];
        }
        
        return response()->json($results);
    }
    
    public function getHomeData()
    {
        $start = (new Carbon('first day of last month'))->toDateString();
        $end = (new Carbon('last day of last month'))->toDateString();
        
        $topFiveMedicines = MmTransactionAddMedicine::withCacheCooldownSeconds(300)->selectRaw('*, SUM(jml_permintaan) as jml_permintaan')
                ->whereRaw("DATE_FORMAT(created_date, '%Y-%m-%d') BETWEEN '{$start}' AND '{$end}'")
                ->with('mmItem')
                ->groupBy('id_barang')
                ->orderByRaw('sum(jml_permintaan) desc')
                ->limit(10)
                ->get();
        
        $countPatientNow = MmPatientRegistration::withCacheCooldownSeconds(300)->whereRaw("DATE_FORMAT(created_date, '%Y-%m-%d') = '". Carbon::now()->toDateString() ."'")
                ->count();
        
        $countPatientPreviousMonth = MmPatientRegistration::withCacheCooldownSeconds(300)->whereRaw("DATE_FORMAT(created_date, '%Y-%m-%d') BETWEEN '{$start}' AND '{$end}'")
                ->count();
        
        $topUsers = \App\User::orderBy('last_login_at', 'desc')
                ->limit(10)
                ->get();
        
        $patientPoly = \App\MmUnit::withCacheCooldownSeconds(100)
                ->select(['mm_unit.*', \DB::raw('COUNT(mm_pasien_pendaftaran.id_pendaftaran) as qty')])
                ->leftJoin('mm_pasien_pendaftaran', 'mm_unit.id_unit', '=', 'mm_pasien_pendaftaran.id_unit')
                ->where('mm_unit.is_poly', 1)
                ->where('mm_unit.is_deleted', 0)
                ->whereRaw('DATE_FORMAT(mm_pasien_pendaftaran.created_date, "%Y-%m-%d") = "' . Carbon::now()->toDateString() . '"')
                ->orderBy('mm_unit.nama_unit', 'asc')
                ->groupBy('mm_unit.id_unit')
                ->get();
        
        return response()->json([
            'status' => 1,
            'data' => [
                'topFiveMedicines' => $topFiveMedicines->toArray(),
                'countPatientNow' => $countPatientNow,
                'countPatientPreviousMonth' => $countPatientPreviousMonth,
                'topUsers' => $topUsers->toArray(),
                'patientPoly' => $patientPoly->toArray()
            ]
        ]);
    }
    
    public function getHomeReportLabel()
    {
        $startDate = Carbon::now()->subMonth(6)->format('Y-m');
        $endDate = Carbon::now()->format('Y-m');
        $dateRanges = \App\Helpers\DateTimeHelper::getArrayDateRange($startDate, $endDate);
        $dateRangeLabels = \App\Helpers\DateTimeHelper::getArrayDateRange($startDate, $endDate, 'M Y');
        
        $ranaps = MmTransactionAddMedicine::select([
                \DB::raw('COUNT(mm_transaksi_add_obat.id_transaksi_obat) as count'), \DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m") as date')
            ])
            ->join('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
            ->whereRaw('(mm_pasien_pendaftaran.kelas_perawatan != 0 OR mm_pasien_pendaftaran.kelas_perawatan is not null)')
            ->whereBetween(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'), [Carbon::now()->subMonth(7)->format('Y-m'), Carbon::now()->format('Y-m')])
            ->groupBy(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'))
            ->pluck('count', 'date');
        
        $rajalJkns = MmTransactionAddMedicine::select([
                \DB::raw('COUNT(mm_transaksi_add_obat.id_transaksi_obat) as count'), \DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m") as date')
            ])
            ->join('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
            ->whereRaw('((mm_pasien_pendaftaran.id_jenis_pembayaran IN (8,9)) AND (mm_pasien_pendaftaran.kelas_perawatan = 0 OR mm_pasien_pendaftaran.kelas_perawatan is null))')
            ->whereBetween(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'), [Carbon::now()->subMonth(7)->format('Y-m'), Carbon::now()->format('Y-m')])
            ->groupBy(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'))
            ->pluck('count', 'date');
        
        $rajalRegulars = MmTransactionAddMedicine::select([
                \DB::raw('COUNT(mm_transaksi_add_obat.id_transaksi_obat) as count'), \DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m") as date')
            ])
            ->join('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
            ->whereRaw('((mm_pasien_pendaftaran.id_jenis_pembayaran = 0) AND (mm_pasien_pendaftaran.kelas_perawatan = 0 OR mm_pasien_pendaftaran.kelas_perawatan is null))')
            ->whereBetween(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'), [Carbon::now()->subMonth(7)->format('Y-m'), Carbon::now()->format('Y-m')])
            ->groupBy(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'))
            ->pluck('count', 'date');
        
        $rajalCompanies = MmTransactionAddMedicine::select([
                \DB::raw('COUNT(mm_transaksi_add_obat.id_transaksi_obat) as count'), \DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m") as date')
            ])
            ->join('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
            ->whereRaw('((mm_pasien_pendaftaran.id_jenis_pembayaran = 4) AND (mm_pasien_pendaftaran.kelas_perawatan = 0 OR mm_pasien_pendaftaran.kelas_perawatan is null))')
            ->whereBetween(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'), [Carbon::now()->subMonth(7)->format('Y-m'), Carbon::now()->format('Y-m')])
            ->groupBy(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'))
            ->pluck('count', 'date');
        
        $rajalIgds = MmTransactionAddMedicine::select([
                \DB::raw('COUNT(mm_transaksi_add_obat.id_transaksi_obat) as count'), \DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m") as date')
            ])
            ->join('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
            ->where('mm_pasien_pendaftaran.id_pendaftaran', 4)
            ->whereBetween(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'), [Carbon::now()->subMonth(7)->format('Y-m'), Carbon::now()->format('Y-m')])
            ->groupBy(\DB::raw('DATE_FORMAT(mm_transaksi_add_obat.created_date, "%Y-%m")'))
            ->pluck('count', 'date');
        
        $resultRanaps = [];
        $resultRajalJkns = [];
        $resultRajalRegulars = [];
        $resultRajalCompanies = [];
        $resultRajalIgds = [];
        
        foreach ($dateRanges as $dateRange) {
            $resultRanaps[] = array_key_exists($dateRange, $ranaps->toArray()) ? $ranaps[$dateRange] : 0;
            $resultRajalJkns[] = array_key_exists($dateRange, $rajalJkns->toArray()) ? $rajalJkns[$dateRange] : 0;
            $resultRajalRegulars[] = array_key_exists($dateRange, $rajalRegulars->toArray()) ? $rajalRegulars[$dateRange] : 0;
            $resultRajalCompanies[] = array_key_exists($dateRange, $rajalCompanies->toArray()) ? $rajalCompanies[$dateRange] : 0;
            $resultRajalIgds[] = array_key_exists($dateRange, $rajalIgds->toArray()) ? $rajalIgds[$dateRange] : 0;
        }
        
//        {
//			"labels": ["January", "February", "March", "April", "May", "June", "July"],
//			"datasets": [{
//				"label": "Dataset 1",
//				"backgroundColor": "rgb(255, 99, 132)",
//				"yAxisID": "y-axis-1",
//				"data": [
//					10,
//					10,
//					10,
//					10,
//					10,
//					10,
//					10
//				]
//			}, {
//				"label": "Dataset 2",
//				"backgroundColor": "rgb(255, 159, 64)",
//				"yAxisID": "y-axis-2",
//				"data": [
//					10,
//					10,
//					10,
//					10,
//					10,
//					10,
//					10
//				]
//			}]
//		}
        //                 ->addNumberColumn('RANAP')
//                 ->addNumberColumn('RAJAL JKN')
//                 ->addNumberColumn('RAJAL REGULER')
//                 ->addNumberColumn('RAJAL PERUSAHAAN')
//                 ->addNumberColumn('IGD')
//        	red: 'rgb(255, 99, 132)',
//	orange: 'rgb(255, 159, 64)',
//	yellow: 'rgb(255, 205, 86)',
//	green: 'rgb(75, 192, 192)',
//	blue: 'rgb(54, 162, 235)',
//	purple: 'rgb(153, 102, 255)',
//	grey: 'rgb(201, 203, 207)'
        return response()->json([
            "labels" => $dateRangeLabels,
            "datasets" => [
                [
                    "label" => "RANAP",
                    "backgroundColor" => "rgb(255, 99, 132)",
    				"yAxisID" => "y-axis-1",
                    "data" => $resultRanaps
                ],
                [
                    "label" => "RAJAL JKN",
                    "backgroundColor" => "rgb(255, 159, 64)",
    				"yAxisID" => "y-axis-2",
                    "data" => $resultRajalJkns
                ],
                [
                    "label" => "RAJAL REGULER",
                    "backgroundColor" => "rgb(255, 205, 86)",
    				"yAxisID" => "y-axis-3",
                    "data" => $resultRajalRegulars
                ],
                [
                    "label" => "RAJAL PERUSAHAAN",
                    "backgroundColor" => "rgb(75, 192, 192)",
    				"yAxisID" => "y-axis-4",
                    "data" => $resultRajalCompanies
                ],
                [
                    "label" => "IGD",
                    "backgroundColor" => "rgb(54, 162, 235)",
    				"yAxisID" => "y-axis-5",
                    "data" => $resultRajalIgds
                ],
            ]
        ]);
    }
}
