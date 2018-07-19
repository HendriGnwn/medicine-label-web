<?php

namespace App\Http\Controllers;

use App\TransactionMedicine;
use App\TransactionMedicineDetail;
use Carbon\Carbon;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class ManuallyController extends Controller
{
	protected $rules = [
		'registered_id' => 'required',
        'medicine_date' => 'required',
		'care_type' => 'required',
        'medical_record_number' => 'nullable',
        'doctor_id' => 'nullable',
		'receipt_number' => 'nullable',
	];


	/**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (!\Auth::user()->getIsRoleSuperadmin()) {
            abort('403', 'Unauthorized this action.');
        }
        
        return view('manually.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $model = new TransactionMedicine();
        
        return view('manually.create', compact('model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        $user = \Auth::user();
        $rules = [];
        if (isset($request->count)) {
            foreach ($request->count as $key => $medicine) {
                $rules['medicine_id.' . $key] = 'required';
            }
        }
        
//        $check = TransactionMedicine::where('registered_id', $request->registered_id)->first();
//        if ($check) {
//            \Session::flash('info', 'Data pasien ini sudah terdaftar, silahkan lihat nomor pendaftaran ' . $check->mmPatientRegistration->no_pendaftaran);
//            goto redirect;
//        }
        
        $this->validate($request, $this->rules + $rules);
        
        \DB::beginTransaction();
        $model = new TransactionMedicine();
        $model->fill($request->all());
        if ($request->doctor_id == null || $request->doctor_id == 0) {
            $model->doctor_id = $request->patient_registration_doctor_id;
        }
        $model->created_by = $user->id;
        $model->save();
        if (isset($request->count)) {
            foreach ($request->count as $key => $medicine) {
                $detail = new TransactionMedicineDetail();
                $detail->transaction_medicine_id = $model->id;
                $detail->medicine_id = $request->medicine_id[$medicine];
                $detail->name = $request->medicine_label[$medicine];
                $detail->quantity = $request->quantity[$medicine];
                $detail->how_to_use = $request->how_to_use[$medicine];
                $detail->save();
            }
        }
        
        $model->storeToBigDatabaseSimrs();
        \DB::commit();
        
        \Session::flash('success', 'Success');
        
        //redirect:
        if ($user->getIsRolePharmacist()) {
            $redirect = route('transaction-medicine.pharmacist');
        } else if ($user->getIsRoleDoctor()) {
            $redirect = route('transaction-medicine.doctor');
        } else {
            $redirect = route('manually.index');
        }
        
        return redirect($redirect);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *Bank
     * @return View
     */
    public function show($id)
    {
        $model = TransactionMedicine::findOrFail($id);

        return view('manually.show', compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return View
     */
    public function edit($id)
    {
        $model = TransactionMedicine::findOrFail($id);

        return view('manually.edit', compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param Request $request
     *
     * @return RedirectResponse|Redirector
     */
    public function update($id, Request $request)
    {
		$rules = $this->rules;
        $this->validate($request, $rules);
        
        $user = \Auth::user();
		$model = TransactionMedicine::findOrFail($id);
		$model->fill($request->all());
        if ($request->doctor_id == null || $request->doctor_id == 0) {
            $model->doctor_id = $request->patient_registration_doctor_id;
        }
        $model->updated_by = $user->id;
        $model->save();
        if (isset($request->count)) {
            foreach ($request->count as $key => $medicine) {
                $detail = TransactionMedicineDetail::find($request->detail_id[$medicine]);
                if (!$detail) {
                    $detail = new TransactionMedicineDetail();
                }
                $detail->transaction_medicine_id = $model->id;
                $detail->medicine_id = $request->medicine_id[$medicine];
                $detail->name = $request->medicine_label[$medicine];
                $detail->quantity = $request->quantity[$medicine];
                $detail->how_to_use = $request->how_to_use[$medicine];
                $detail->drink = $request->drink[$medicine];
                $detail->save();
            }
        }
        
        $model->updateToBigDatabaseSimrs();
		
        Session::flash('success', 'Concept updated!');

        if ($user->getIsRolePharmacist()) {
            $redirect = route('transaction-medicine.pharmacist');
        } else if ($user->getIsRoleDoctor()) {
            $redirect = route('transaction-medicine.doctor');
        } else {
            $redirect = route('manually.index');
        }
        
        return redirect($redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return RedirectResponse|Redirector
     */
    public function destroy($id)
    {
        TransactionMedicine::destroy($id);
        
        Session::flash('success', 'Delete deleted!');

        return redirect('manually');
    }
    
    public function printPreview($id)
    {
        $model = TransactionMedicine::findOrFail($id);
        
        return view('manually.print-preview', compact('model'));
    }
	
	/**
	 * any data
	 */
	public function listIndex(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $model = TransactionMedicine::select([
					DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'transaction_medicine.*'
				]);

         $datatables = app('datatables')->of($model)
            ->editColumn('registered_id', function ($model) {
                return $model->mmPatientRegistration ? $model->mmPatientRegistration->no_pendaftaran : $model->registered_id;
            })
            ->editColumn('doctor_id', function ($model) {
                return $model->mmDoctor->nama_dokter;
            })
            ->editColumn('medical_record_number', function ($model) {
                return $model->medical_record_number . ' - ' . $model->mmPatient->nama;
            })
            ->editColumn('care_type', function ($model) {
                return $model->getCareTypeLabel();
            })
            ->editColumn('created_by', function ($model) {
                return $model->getCreatedName();
            })
            ->editColumn('updated_by', function ($model) {
                return $model->getUpdatedName();
            })
            ->addColumn('action', function ($model) {
                $printUrl = route('manually.print-preview', ['id' => $model->id]);
                $editUrl = route('manually.edit', ['id' => $model->id]);
                return "<a href='{$printUrl}' target='_blank' class='btn btn-xs btn-success btn-rounded' data-toggle='tooltip' title='Print'><i class='fa fa-print'></i></a> "
                    . "<a href='{$editUrl}' class='btn btn-xs btn-primary btn-rounded' data-toggle='tooltip' title='Edit'><i class='fa fa-edit'></i></a> "
                    . "<a href='#' onclick='deleteRecord({$model->id})' class='btn btn-xs btn-danger btn-rounded' data-toggle='tooltip' title='Hapus'><i class='fa fa-trash'></i></a>";
            });

        if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
        }

        if ($range = $datatables->request->get('created_range')) {
            $rang = explode("-", $range);
            $startDate = Carbon::parse($rang[0])->toDateString();
            $endDate = Carbon::parse($rang[1])->toDateString();
            $datatables->whereBetween('transaction_medicine.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
        
        if ($range = $datatables->request->get('updated_range')) {
            $rang = explode("-", $range);
            $startDate = Carbon::parse($rang[0])->toDateString();
            $endDate = Carbon::parse($rang[1])->toDateString();
            $datatables->whereBetween('transaction_medicine.updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
		
        return $datatables->make(true);
    }
}
