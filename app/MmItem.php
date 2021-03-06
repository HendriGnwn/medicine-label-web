<?php

namespace App;

use Carbon\Carbon;

class MmItem extends BaseModel
{
    protected $connection = 'mysqlMm';
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'mm_barang';
    
    protected $primaryKey = 'id_barang';
    
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_gudang',
        'kode_lokasi_gudang',
        'kode_lokasi_gudang',
        'kode_lokasi_apotek',
        'id_barang_group',
        'id_barang_satuan_besar',
        'id_barang_satuan_kecil',
        'id_lokasi_barang',
        'kode_barang',
        'nama_barang',
        'harga_jual',
        'harga_jual_resep',
        'hpp',
        'stok',
        'kode_pengali',
        'kode_group',
        'jenis',
        'isi',
        'kode_generik',
        'id_formularium',
        'is_ekatalog',
        'is_fornas',
        'is_apotek',
        'tmp_stok',
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
    
    public function mmItemSmall()
    {
        return $this->hasOne('\App\MmItemSmall', 'id_barang_satuan_kecil', 'id_barang_satuan_kecil');
    }
    
    public function mmMasterStocks()
    {
        return $this->hasMany('\App\MmMasterStock', 'id_barang', 'id_barang')->orderBy('id_stok', 'asc');
    }
    
    public function mmAvailableMasterStocks()
    {
        return $this->mmMasterStocks()->where('stok', '>', 0);
    }
    
    public function getItemExpiredAt()
    {
        if (is_null($this->mmAvailableMasterStocks))
        {
            return null;
        }
        
        foreach ($this->mmAvailableMasterStocks as $masterStock) {
            return $masterStock->kadaluarsa;
        }
    }
    
    public function getFormattedItemExpiredAt()
    {
        if (is_null($this->getItemExpiredAt())) {
            return null;
        }
        
        return Carbon::parse($this->getItemExpiredAt())->format('d/m/Y');
    }
}
