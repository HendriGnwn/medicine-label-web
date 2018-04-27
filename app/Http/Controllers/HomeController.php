<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \App\User::find(\Auth::user()->id);
        if ($user->getIsRolePharmacist()) {
            $redirect = route('transaction-medicine.pharmacist');
        } else if ($user->getIsRoleDoctor()) {
            $redirect = route('transaction-medicine.doctor');
        } else {
            $redirect = route('manually.index');
        }
        
        return redirect($redirect);
        
        return view('home');
    }
}
