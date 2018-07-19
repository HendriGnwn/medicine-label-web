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

class HowToUseController extends Controller
{
	protected $rules = [
		'id_barang_satuan_kecil' => 'required',
        'nama' => 'required',
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
        
        return view('how-to-use.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $model = new \App\MmHowToUse();
        
        return view('how-to-use.create', compact('model'));
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
        
        $model = new \App\MmHowToUse();
        $model->fill($request->all());
        $model->created_date = Carbon::now()->toDateTimeString();
        $model->created_by = \App\User::CREATED_BY;
        $model->save();
        
        \Session::flash('success', 'Success');
        
        return redirect(route('how-to-use.index'));
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
        $model = MmHowToUse::findOrFail($id);

        return view('how-to-use.show', compact('model'));
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
        $model = \App\MmHowToUse::findOrFail($id);

        return view('how-to-use.edit', compact('model'));
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
        
        $model = \App\MmHowToUse::findOrFail($id);
        $model->fill($request->all());
        $model->last_modified_date = Carbon::now()->toDateTimeString();
        $model->last_modified_by = \App\User::CREATED_BY;
        $model->save();
        
        \Session::flash('success', 'Success');
        
        return redirect(route('how-to-use.index'));
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
        \App\MmHowToUse::destroy($id);
        
        Session::flash('success', 'Delete deleted!');

        return redirect(route('how-to-use.index'));
    }
	
	/**
	 * any data
	 */
	public function listIndex(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $model = \App\MmHowToUse::with(['mmItemSmall'])
                ->select([
					DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'mm_aturan_pakai.*'
				]);

         $datatables = app('datatables')->of($model)
            ->editColumn('id_barang_satuan_kecil', function ($model) {
                return $model->mmItemSmall ? $model->mmItemSmall->nama_satuan_kecil : $model->id_barang_satuan_kecil;
            })
            ->addColumn('action', function ($model) {
                $editUrl = route('how-to-use.edit', ['id' => $model->id_aturan_pakai]);
                return "<a href='{$editUrl}' class='btn btn-xs btn-primary btn-rounded' data-toggle='tooltip' title='Edit'><i class='fa fa-edit'></i></a> "
                    . "<a href='#' onclick='deleteRecord({$model->id_aturan_pakai})' class='btn btn-xs btn-danger btn-rounded' data-toggle='tooltip' title='Hapus'><i class='fa fa-trash'></i></a>";
            });

        if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
        }

        if ($range = $datatables->request->get('created_range')) {
            $rang = explode("-", $range);
            $startDate = Carbon::parse($rang[0])->toDateString();
            $endDate = Carbon::parse($rang[1])->toDateString();
            $datatables->whereBetween('mm_aturan_pakai.created_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
        
        if ($range = $datatables->request->get('updated_range')) {
            $rang = explode("-", $range);
            $startDate = Carbon::parse($rang[0])->toDateString();
            $endDate = Carbon::parse($rang[1])->toDateString();
            $datatables->whereBetween('mm_aturan_pakai.last_modified_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
		
        return $datatables->make(true);
    }
}
