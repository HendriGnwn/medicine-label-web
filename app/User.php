<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    
    const ROLE_SUPERADMIN = 1;
    const ROLE_APOTEKER = 5;
    const ROLE_DOCTOR = 10;
    
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dcl_user_id', 
        'nip', 
        'name', 
        'username', 
        'password',
        'role',
        'apoteker_name',
        'apoteker_sik',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function dclUser()
    {
        return $this->hasOne('\App\DclUser', 'user_id', 'dcl_user_id');
    }
    
    public function mmDoctors()
    {
        return $this->hasMany('\App\MmDoctor', 'nip', 'nip');
    }
}
