<?php

namespace App\Http\Controllers;

use App\TransactionMedicine;
use App\TransactionMedicineDetail;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class ManuallyController extends Controller
{
	protected $rules = [
		'doctor_id' => 'required',
		'registered_id' => 'nullable',
		'medical_record_number' => 'required',
        'medicine_date' => 'required',
		'care_type' => 'required',
		'receipt_number' => 'nullable',
	];


	/**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
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
        $rules = [];
        if (isset($request->count)) {
            foreach ($request->count as $key => $medicine) {
                $rules['medicine_id.' . $key] = 'required';
            }
        }
        
        $this->validate($request, $this->rules + $rules);
        
        $model = new TransactionMedicine();
        $model->fill($request->all());
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
        
        \Session::flash('success', 'Success');
        
        return redirect('manually');
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
        $model = \App\TransactionMedicine::findOrFail($id);

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
        
		$model = TransactionMedicine::findOrFail($id);
		$model->fill($request->all());
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
		
        Session::flash('success', 'Concept updated!');

        return redirect('manually');
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
            ->editColumn('doctor_id', function ($model) {
                return $model->mmDoctor->nama_dokter;
            })
            ->editColumn('medical_record_number', function ($model) {
                return $model->medical_record_number . ' - ' . $model->mmPatient->nama;
            })
            ->editColumn('care_type', function ($model) {
                return $model->getCareTypeLabel();
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

//        if ($range = $datatables->request->get('range')) {
//            $rang = explode(":", $range);
//            if($rang[0] != "Invalid date" && $rang[1] != "Invalid date" && $rang[0] != $rang[1]){
//                $datatables->whereBetween('concept.created_at', ["$rang[0] 00:00:00", "$rang[1] 23:59:59"]);
//            }else if($rang[0] != "Invalid date" && $rang[1] != "Invalid date" && $rang[0] == $rang[1]) {
//                $datatables->whereBetween('concept.created_at', ["$rang[0] 00:00:00", "$rang[1] 23:59:59"]);
//            }
//        }
		
        return $datatables->make(true);
    }
}