<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use function route;
use function view;

class SettingController extends Controller
{
	protected $rules = [
		'address' => 'required',
		'apoteker' => 'required',
		'sik' => 'required',
	];


	/**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $model = Setting::findOrFail(Setting::SETTING_FIRST);
        return view('setting.index', compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param Request $request
     *
     * @return RedirectResponse|Redirector
     */
    public function update(Request $request)
    {
		$rules = $this->rules;
        $this->validate($request, $rules);
		
		$model = Setting::findOrFail(Setting::SETTING_FIRST);
		
        $requestData = $request->all();
		
		$model->fill($requestData);
        $model->save();
		
        Session::flash('success', 'Success');

        return redirect(route('setting.index'));
    }
}
