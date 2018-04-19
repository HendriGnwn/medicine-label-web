<?php

namespace App;

class MmMasterStock extends BaseModel
{
    protected $connection = 'mysqlMm';
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'mm_master_stok';
    
    protected $primaryKey = 'id_stok';
    
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_barang',
        'kode_barang',
        'id_penerimaan',
        'stok',
        'harga',
        'isi',
        'no_faktur',
        'tanggal_faktur',
        'no_batch',
        'kadaluarsa',
        'id_lokasi_barang',
        'lokasi_barang_2',
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
    
    public function mmItem()
    {
        return $this->hasOne('\App\MmItem', 'id_barang', 'id_barang');
    }
}
