<?php

namespace App\Http\Controllers;

use App\TransactionMedicine;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionAddMedicineController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('mm-transaction-add-medicine.index');
    }
    
    public function edit($id)
    {
        $model = \App\MmPatientRegistration::where('no_pendaftaran', $id)->first();
        return view('mm-transaction-add-medicine.edit', compact('model'));
    }
    
    public function update($id, Request $request)
    {
        $patientRegistration = \App\MmPatientRegistration::where('no_pendaftaran', $id)->first();
        if (!$patientRegistration) {
            abort('404', 'not found');
        }
        $medicine = \App\TransactionAddMedicineAdditional::where('patient_registration_id', $patientRegistration->id_pendaftaran)->first();
        if (!$medicine) {
            $medicine = new \App\TransactionAddMedicineAdditional();
            $medicine->patient_registration_id = $patientRegistration->id_pendaftaran;
        }
        $medicine->save();
        
        $no = 0;
        foreach ($request->id as $medicineId) {
            if ($medicineId == null) {
                continue;
            }
            $model = \App\TransactionAddMedicineAdditionalDetail::where('transaction_medicine_id', $medicineId)->first();
            if (!$model) {
                $model = new \App\TransactionAddMedicineAdditionalDetail();
                $model->transaction_medicine_id = $medicineId;
                $model->created_by = \Auth::user()->id;
            }
            $model->transaction_add_medicine_additional_id = $medicine->id;
            $model->how_to_use = $request->how_to_use[$no];
            $model->receipt_number = $request->receipt_number;
            $model->updated_by = \Auth::user()->id;
            $model->save();
            $no++;
        }
        
        return redirect(route('transaction-add-medicine.print', ['id' => $id]));
    }
    
    public function printPreview($id)
    {
        $model = \App\MmPatientRegistration::where('no_pendaftaran', $id)->first();
        return view('mm-transaction-add-medicine.print-preview', compact('model'));
    }
    
    public function postPrint($id)
    {
        $patientRegistration = \App\MmPatientRegistration::where('no_pendaftaran', $id)->first();
        if (!$patientRegistration) {
            abort('404', 'not found');
        }
        $medicine = \App\TransactionAddMedicineAdditional::where('patient_registration_id', $patientRegistration->id_pendaftaran)->first();
        if (!$medicine) {
            $medicine = new \App\TransactionAddMedicineAdditional();
            $medicine->patient_registration_id = $patientRegistration->id_pendaftaran;
        }
        $medicine->print_count = $medicine->print_count + 1;
        $medicine->save();
        
        return response()->json([
            'status' => 'success'
        ]);
    }
    
	/**
	 * any data
	 */
	public function listIndex(Request $request)
    {
        \DB::statement(DB::raw('set @rownum=0'));
        $model = \App\MmTransactionAddMedicine::select([
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
                $transactionAddMedicineAdditional = \App\TransactionAddMedicineAdditional::where('patient_registration_id', $model->mmPatientRegistration->id_pendaftaran)->first();
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
