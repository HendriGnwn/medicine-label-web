<?php

namespace App\Http\Controllers;

use App\TransactionMedicine;
use App\TransactionMedicineDetail;
use Carbon\Carbon;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $topFiveMedicines = TransactionMedicineDetail::selectRaw('*, SUM(quantity) as quantity')
                ->groupBy('medicine_id')
                ->orderByRaw('sum(quantity) desc')
                ->limit(10)
                ->get();
        
        $countPatientNow = TransactionMedicine::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '". Carbon::now()->toDateString() ."'")
                ->count();
        
        $start = (new Carbon('first day of last month'))->toDateString();
        $end = (new Carbon('last day of last month'))->toDateString();

        $countPatientPreviousMonth = TransactionMedicine::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '{$start}' AND '{$end}'")
                ->count();
        
        
//        $user = \App\User::find(\Auth::user()->id);
//        if ($user->getIsRolePharmacist()) {
//            $redirect = route('transaction-medicine.pharmacist');
//        } else if ($user->getIsRoleDoctor()) {
//            $redirect = route('transaction-medicine.doctor');
//        } else {
//            $redirect = route('manually.index');
//        }
//        
//        return redirect($redirect);
        
        return view('home', compact('topFiveMedicines', 'countPatientNow', 'countPatientPreviousMonth'));
    }
}
