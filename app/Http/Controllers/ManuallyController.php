<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ManuallyController extends Controller
{
	protected $rules = [
		'payment_id' => 'nullable',
		'doctor_id' => 'required',
		'registered_id' => 'nullable',
		'medical_record_number' => 'required',
		'care_type' => 'required',
		'approval_status' => 'nullable',
		'payment_detail_date' => 'nullable',
		'payment_detail_status' => 'nullable',
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
        return view('manually.create');
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
        $this->validate($request, $this->rules);
        
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
        $model = \App\Concept::findOrFail($id);

        return view('admin.concept.show', compact('model'));
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
        $model = \App\Concept::findOrFail($id);

        return view('admin.concept.edit', compact('model'));
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
		
		$model = \App\Concept::findOrFail($id);
		
        $requestData = $request->all();
		
		$model->fill($requestData);
        $model->save();
		
        Session::flash('success', 'Concept updated!');

        return redirect('admin/concept');
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
        return redirect('admin/concept');
		
        Session::flash('success', 'Concept deleted!');

        return redirect('admin/concept');
    }
	
	/**
	 * any data
	 */
	public function listIndex(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $model = \App\TransactionMedicine::select([
					DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'transaction_medicine.*'
				]);

         $datatables = app('datatables')->of($model)
            ->addColumn('action', function ($model) {
                return //'<a href="concept/'.$model->id.'" class="btn btn-xs btn-success rounded" data-toggle="tooltip" title="" data-original-title="'. trans('systems.edit') .'"><i class="fa fa-eye"></i></a> '
						 '<a href="concept/'.$model->id.'/edit" class="btn btn-xs btn-primary rounded" data-toggle="tooltip" title="" data-original-title="'. trans('systems.edit') .'"><i class="fa fa-pencil"></i></a> ';
						//. '<a onclick="modalDelete('.$model->id.')" class="btn btn-xs btn-danger rounded" data-toggle="tooltip" title="" data-original-title="'. trans('systems.delete') .'"><i class="fa fa-trash"></i></a>';
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
