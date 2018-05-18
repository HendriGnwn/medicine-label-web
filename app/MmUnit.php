<?php

namespace App;

class MmUnit extends BaseModel
{
    protected $connection = 'mysqlMm';
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'mm_unit';
    
    protected $primaryKey = 'id_unit';
    
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dcl_group_id',
        'kode_unit',
        'nama_unit',
        'group_unit',
        'is_poly',
        'id_shift',
        'nama_group_unit',
        'pendapatan_unit',
        'tipe_pasien',
        'is_holiday_open',
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
}
