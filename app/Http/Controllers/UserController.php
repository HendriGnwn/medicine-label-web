<?php

namespace App\Http\Controllers;

use App\User;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use function route;
use function view;

class UserController extends Controller
{
	protected $rules = [
		'name' => 'required',
		'username' => 'required|unique:user,username',
		'password' => 'required',
		'role' => 'required',
		'apoteker_name' => 'nullable',
		'apoteker_sik' => 'nullable',
	];
    
	/**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (!\Auth::user()->getIsRoleSuperadmin()) {
            return redirect(route('home'));
        }
        
        return view('user.index');
    }
    
    public function create()
    {
        return view('user.create');
    }
    
    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        
        $model = new User();
        $model->fill($request->all());
        $model->password = bcrypt($request->password);
        $model->save();
        
        \Session::flash('success', 'Success');
        
        return redirect(route('user.index'));
    }
    
    public function edit($id)
    {
        $model = User::findOrFail($id);
        
        return view('user.edit', compact('model'));
    }
    
    public function editProfile()
    {
        $model = User::findOrFail(\Auth::user()->id);
        
        return view('user.edit-profile', compact('model'));
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
        $rules['username'] = 'required|unique:user,username,' . $id;
        unset($rules['password']);
        unset($rules['role']);
        $this->validate($request, $rules);
        
		$model = User::findOrFail($id);
        $requestData = $request->all();
        if (empty($request->password)) {
            unset($requestData['password']);
        } else {
            $requestData['password'] = bcrypt($requestData['password']);
        }
		$model->fill($requestData);
        $model->save();
		
        Session::flash('success', 'Update Success');

        return redirect(route('user.index'));
    }
    
    public function destroy($id)
    {
        User::destroy($id);
        return redirect(route('user.index'));
    }
    
    public function listIndex(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $model = User::select([
					DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'user.*'
				]);

         $datatables = app('datatables')->of($model)
            ->editColumn('role', function ($model) {
                return $model->getRoleLabel();
            })
            ->addColumn('action', function ($model) {
                $editUrl = route('user.edit', ['id' => $model->id]);
                return "<a href='{$editUrl}' class='btn btn-xs btn-primary btn-rounded' data-toggle='tooltip' title='Edit'><i class='fa fa-edit'></i></a> "
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
