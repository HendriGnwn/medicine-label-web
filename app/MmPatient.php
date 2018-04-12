<?php

namespace App;

class MmPatient extends BaseModel
{
    protected $connection = 'mysqlMm';
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'mm_pasien';
    
    protected $primaryKey = 'id_pasien';
    
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no_rekam_medis',
        'title',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'id_kelurahan_ref',
        'id_kelurahan',
        'id_kecamatan',
        'id_kota',
        'id_provinsi',
        'no_telepon',
        'no_ktp',
        'agama',
        'parent_nomor',
        'id_pasien_luar',
        'id_pendaftaran_aps',
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
