<?php

namespace App\Http\Controllers;

use App\MmTransactionAddMedicine;
use App\TransactionAddMedicineAdditional;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(Request $request)
    {
        $datePeriod = $request->get('date_period', Carbon::now()->format('m/d/Y'));
        $startDate = Carbon::parse($datePeriod)->toDateString() . ' 00:00:00';
        $endDate = Carbon::parse($datePeriod)->toDateString() . ' 23:59:59';
        
        $models = \App\MmPatientRegistration::leftJoin('mm_transaksi_add_obat', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
            ->whereBetween('mm_transaksi_add_obat.created_date', [$startDate, $endDate])
            ->groupBy('mm_pasien_pendaftaran.id_pendaftaran')
            ->get();
        $medicines = \App\MmTransactionAddMedicine::whereBetween('mm_transaksi_add_obat.created_date', [$startDate, $endDate])
            ->groupBy('mm_transaksi_add_obat.id_barang')
            ->get();
        
        return view('report.index', compact('models', 'medicines'));
    }
    
	/**
	 * any data
	 */
	public function listIndex(Request $request)
    {
        \DB::statement(DB::raw('set @rownum=0'));
        $model = MmTransactionAddMedicine::select([
					DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'mm_transaksi_add_obat.*'
				])
                ->join('mm_pasien_pendaftaran', 'mm_pasien_pendaftaran.id_pendaftaran', '=', 'mm_transaksi_add_obat.id_pendaftaran')
                ->groupBy('id_pembayaran', 'id_pendaftaran');

         $datatables = app('datatables')->of($model)
            ->addIndexColumn()
            ->editColumn('id_pendaftaran', function ($model) {
                return $model->mmPatientRegistration ? $model->mmPatientRegistration->no_pendaftaran : $model->id_pendaftaran;
            })
            ->editColumn('no_rekam_medis', function ($model) {
                return $model->no_rekam_medis . ' - ' . $model->mmPatient->nama;
            })
            ->editColumn('tipe_rawatan', function ($model) {
                return $model->getTipeRawatanLabel();
            })
            ->editColumn('id_dokter', function ($model) {
                return $model->id_unit . ' - ' . ($model->mmDoctor ? $model->mmDoctor->nama_dokter : null);
            })
            ->addColumn('print_count', function ($model) {
                $transactionAddMedicineAdditional = TransactionAddMedicineAdditional::where('patient_registration_id', $model->mmPatientRegistration->id_pendaftaran)->first();
                if (!$transactionAddMedicineAdditional) {
                    return '0';
                }
                return $transactionAddMedicineAdditional->print_count;
            })
            ->addColumn('action', function ($model) {
                $editUrl = route('transaction-add-medicine.edit', ['id' => $model->mmPatientRegistration->no_pendaftaran]);
                $printUrl = route('transaction-add-medicine.print', ['id' => $model->mmPatientRegistration->no_pendaftaran]);
                return "<a href='{$editUrl}' class='btn btn-xs btn-primary btn-rounded' title='Update' target='_blank'><i class='fa fa-edit'></i></a>"
                       . " <a href='{$printUrl}' class='btn btn-xs btn-success btn-rounded' title='Print' target='_blank'><i class='fa fa-print'></i></a> ";
            });

        if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
        }

        if ($registrationNumber = $datatables->request->get('registration_number')) {
            $datatables->where('mm_pasien_pendaftaran.no_pendaftaran', 'like', "%{$registrationNumber}%");
        }
        
        if ($medicalRecordNumber = $datatables->request->get('medical_record_number')) {
            $datatables->where('mm_transaksi_add_obat.no_rekam_medis', 'like', "%{$medicalRecordNumber}%");
        }
        
        if ($doctor = $datatables->request->get('doctor_id')) {
            $datatables->where('mm_transaksi_add_obat.id_dokter', 'like', "%{$doctor}%");
        }
		
        return $datatables->make(true);
    }
}
