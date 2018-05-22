<?php

namespace App;

class MmDoctor extends BaseModel
{
    protected $connection = 'mysqlMm';
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'mm_dokter';
    
    protected $primaryKey = 'id_dokter';
    
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_unit',
        'nip',
        'nama_dokter',
        'tipe_dokter',
        'status_aktif',
        'additional_data',
        'created_date',
        'created_by',
        'modified_count',
        'last_modified_date',
        'last_modified_by',
        'is_deleted',
        'deleted_date',
        'deleted_by',
    ];
    
    public function getArrayDoctorsId()
    {
        return MmDoctor::where('nama_dokter', $this->nama_dokter)->pluck('id_dokter');
    }
}
