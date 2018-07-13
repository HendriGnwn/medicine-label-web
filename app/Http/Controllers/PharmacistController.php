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

class PharmacistController extends Controller
{
	protected $rules = [
		'name' => 'required',
        'sik' => 'required',
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
        
        return view('pharmacist.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $model = new \App\Pharmacist();
        
        return view('pharmacist.create', compact('model'));
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
        
        $model = new \App\Pharmacist();
        $model->fill($request->all());
        $model->created_at = Carbon::now()->toDateTimeString();
        $model->save();
        
        \Session::flash('success', 'Success');
        
        return redirect(route('pharmacist.index'));
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

        return view('pharmacist.show', compact('model'));
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
        $model = \App\Pharmacist::findOrFail($id);

        return view('pharmacist.edit', compact('model'));
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
		$this->validate($request, $this->rules);
        
        $model = \App\Pharmacist::findOrFail($id);
        $model->fill($request->all());
        $model->created_at = Carbon::now()->toDateTimeString();
        $model->save();
        
        \Session::flash('success', 'Success');
        
        return redirect(route('pharmacist.index'));
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
        \App\Pharmacist::destroy($id);
        
        Session::flash('success', 'Delete deleted!');

        return redirect(route('pharmacist.index'));
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
        $model = \App\Pharmacist::select([
					DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'pharmacist.*'
				]);

         $datatables = app('datatables')->of($model)
            ->addColumn('action', function ($model) {
                $editUrl = route('pharmacist.edit', ['id' => $model->id]);
                return "<a href='{$editUrl}' class='btn btn-xs btn-primary btn-rounded' data-toggle='tooltip' title='Edit'><i class='fa fa-edit'></i></a> "
                    . "<a href='#' onclick='deleteRecord({$model->id})' class='btn btn-xs btn-danger btn-rounded' data-toggle='tooltip' title='Hapus'><i class='fa fa-trash'></i></a>";
            });

        if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
        }

        if ($range = $datatables->request->get('created_range')) {
            $rang = explode("-", $range);
            $startDate = Carbon::parse($rang[0])->toDateString();
            $endDate = Carbon::parse($rang[1])->toDateString();
            $datatables->whereBetween('pharmacist.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
        
        if ($range = $datatables->request->get('updated_range')) {
            $rang = explode("-", $range);
            $startDate = Carbon::parse($rang[0])->toDateString();
            $endDate = Carbon::parse($rang[1])->toDateString();
            $datatables->whereBetween('pharmacist.updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
		
        return $datatables->make(true);
    }
}
