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
                ->orWhere('no_pendaftaran', 'like', "%$term%")
                
                ->whereRaw('(id_dokter != "0" OR id_dokter IS NOT NULL)')
                ->orderBy('tanggal_pendaftaran', 'desc')
                ->limit(20)
                ->get();
        $results = [];
        foreach ($patients as $patient) {
            $results[] = [
                'id' => $patient->id_pendaftaran,
                'text' => $patient->no_pendaftaran . ' - ' . $patient->no_rekam_medis . ' - ' . $patient->mmPatient->nama,
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
        
        $countPatientNow = MmPatientRegistration::withCacheCooldownSeconds(300)->whereRaw("DATE_FORMAT(tanggal_pendaftaran, '%Y-%m-%d') = '". Carbon::now()->toDateString() ."'")
                ->count();
        
        $countPatientPreviousMonth = MmPatientRegistration::withCacheCooldownSeconds(300)->whereRaw("DATE_FORMAT(tanggal_pendaftaran, '%Y-%m-%d') BETWEEN '{$start}' AND '{$end}'")
                ->count();
        
        return response()->json([
            'status' => 1,
            'data' => [
                'topFiveMedicines' => $topFiveMedicines->toArray(),
                'countPatientNow' => $countPatientNow,
                'countPatientPreviousMonth' => $countPatientPreviousMonth,
            ]
        ]);
    }
}
