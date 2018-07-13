<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, \Illuminate\Database\Eloquent\SoftDeletes;
    
    const ROLE_SUPERADMIN = 1;
    const ROLE_PHARMACIST = 5;
    const ROLE_DOCTOR = 10;
    
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dcl_user_id', 
        'pharmacist_id', 
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
    
    public function mmDoctor()
    {
        return $this->hasOne('\App\MmDoctor', 'nip', 'nip');
    }
    
    public function getMmDoctorPrimaryKey()
    {
        if (!$this->dclUser) {
            return $this->mmDoctor ? $this->mmDoctor->id_dokter : null;
        }
        
        if (!$this->dclUser->mmDoctors) {
            return null;
        }
        
        foreach ($this->dclUser->mmDoctors as $doctor) {
            return $doctor->id_dokter;
        }
    }
    
    public static function roleLabels()
    {
        return [
            self::ROLE_SUPERADMIN => 'Super Admin',
            self::ROLE_DOCTOR => 'Dokter',
            self::ROLE_PHARMACIST => 'Apoteker',
        ];
    }
    
    public function getRoleLabel()
    {
        $list = self::roleLabels();
        return ($list[$this->role]) ? $list[$this->role] : $this->role;
    }
    
    public function getIsRoleDoctor() {
        return $this->role == self::ROLE_DOCTOR;
    }
    
    public function getIsRolePharmacist() {
        return $this->role == self::ROLE_PHARMACIST;
    }
    
    public function getIsRoleSuperadmin() {
        return $this->role == self::ROLE_SUPERADMIN;
    }
    
    public static function getArrayListDoctors()
    {
        $models = User::where('role', self::ROLE_DOCTOR)->pluck('id')->toArray();
        $users = User::where('role', self::ROLE_SUPERADMIN)->pluck('id')->toArray();

        //return array_merge($model, $users);
        return $models;
    }
}
