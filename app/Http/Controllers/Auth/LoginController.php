<?php

namespace App\Http\Controllers\Auth;

use App\DclUser;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    
    public function username()
    {
        return 'username';
    }
    
    
    /**
     * Attempt to log the user into the application.
     *
     * @param  Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $login = $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
        if ($login) {
            return true;
        }
        
        $loginDclUser = DclUser::where('name', $request->{$this->username()})
                            ->where('name', $request->password)
                            ->where('status', 1)
                            ->whereNotNull('nip')
                            ->first();
        if (!$loginDclUser) {
            return false;
        }
        
        if (!$loginDclUser->mmDoctors) {
            return false;
        }
        
        $user = new User();
        $user->dcl_user_id = $loginDclUser->user_id;
        $user->nip = $loginDclUser->nip;
        $user->name = $loginDclUser->full_name;
        $user->username = $loginDclUser->name;
        $user->password = bcrypt($loginDclUser->name);
        $user->role = User::ROLE_DOCTOR;
        $user->save();
        
        return true;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
