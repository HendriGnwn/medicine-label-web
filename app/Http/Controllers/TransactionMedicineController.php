<?php

namespace App\Http\Controllers;

use App\TransactionMedicine;
use DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionMedicineController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function doctor()
    {
        if (\Auth::user()->getIsRolePharmacist()) {
            abort('403', 'Unauthorized this action.');
        }
        
        return view('transaction-medicine.doctor');
    }
    
	/**
	 * any data
	 */
	public function listDoctorData(Request $request)
    {
        \DB::statement(DB::raw('set @rownum=0'));
        $model = TransactionMedicine::select([
					DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'transaction_medicine.*'
				])
                ->where('transaction_medicine.created_by', \Auth::user()->id);

         $datatables = app('datatables')->of($model)
            ->editColumn('medical_record_number', function ($model) {
                return $model->medical_record_number . ' - ' . $model->mmPatient->nama;
            })
            ->editColumn('created_by', function ($model) {
                return $model->getCreatedName();
            })
            ->editColumn('updated_by', function ($model) {
                return $model->getUpdatedName();
            })
            ->editColumn('care_type', function ($model) {
                return $model->getCareTypeLabel();
            })
            ->addColumn('action', function ($model) {
                $editUrl = route('manually.edit', ['id' => $model->id]);
                return "<a href='{$editUrl}' class='btn btn-xs btn-primary btn-rounded' data-toggle='tooltip' title='Edit'><i class='fa fa-edit'></i></a> ";
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
    
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function pharmacist()
    {
        if (\Auth::user()->getIsRoleDoctor()) {
            abort('403', 'Unauthorized this action.');
        }
        
        return view('transaction-medicine.pharmacist');
    }
    
	/**
	 * any data
	 */
	public function listPharmacistData(Request $request)
    {
        \DB::statement(DB::raw('set @rownum=0'));
        $model = TransactionMedicine::select([
					DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'transaction_medicine.*'
				])
                ->whereIn('transaction_medicine.created_by', array_merge(\App\User::getArrayListDoctors(), [\Auth::user()->id]));

         $datatables = app('datatables')->of($model)
            ->editColumn('medical_record_number', function ($model) {
                return $model->medical_record_number . ' - ' . $model->mmPatient->nama;
            })
            ->editColumn('created_by', function ($model) {
                return $model->getCreatedName();
            })
            ->editColumn('updated_by', function ($model) {
                return $model->getUpdatedName();
            })
            ->editColumn('care_type', function ($model) {
                return $model->getCareTypeLabel();
            })
            ->addColumn('action', function ($model) {
                $printUrl = route('manually.print-preview', ['id' => $model->id]);
                $editUrl = route('manually.edit', ['id' => $model->id]);
                if ($model->created_by == \Auth::user()->id) {
                    return "<a href='{$printUrl}' target='_blank' class='btn btn-xs btn-success btn-rounded' data-toggle='tooltip' title='Print'><i class='fa fa-print'></i></a> "
                        . "<a href='{$editUrl}' class='btn btn-xs btn-primary btn-rounded' data-toggle='tooltip' title='Edit'><i class='fa fa-edit'></i></a> ";
                }
                return "<a href='{$printUrl}' target='_blank' class='btn btn-xs btn-success btn-rounded' data-toggle='tooltip' title='Print'><i class='fa fa-print'></i></a> ";
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
