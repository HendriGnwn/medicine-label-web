<?php

namespace App;

class DclUser extends BaseModel
{
    protected $connection = 'mysqlDcl';
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'dcl_user';
    
    protected $primaryKey = 'user_id';
    
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'pass',
        'salt',
        'full_name',
        'nip',
        'email',
        'upload_ttd',
        'created',
        'updated',
        'login',
        'status',
        'email_expired',
        'email_created',
        'id_status',
        'email_status',
    ];
    
    public function mmDoctors()
    {
        return $this->hasMany('\App\MmDoctor', 'nip', 'nip');
    }
}
