<?php

namespace App;

use Carbon\Carbon;

class MmTransactionPayment extends BaseModel
{
    protected $connection = 'mysqlMm';
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'mm_transaksi_pembayaran';
    
    protected $primaryKey = 'id_pembayaran';
    
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_pembayaran',
        'id_pendaftaran',
        'id_dokter',
        'id_unit',
        'id_barang',
        'no_rekam_medis',
        'status_detail_pembayaran',
        'tanggal_detail_pembayaran',
        'tipe_rawatan',
        'status_approve',
        'jml_permintaan',
        'harga',
        'no_resep',
        'no_transaksi',
        'is_generate',
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
    
    public function mmPayment()
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
