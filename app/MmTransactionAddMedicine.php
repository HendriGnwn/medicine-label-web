<?php

namespace App;

class MmTransactionAddMedicine extends BaseModel
{
    protected $connection = 'mysqlMm';
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'mm_transaksi_add_obat';
    
    protected $primaryKey = 'id_transaksi_obat';
    
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
    
    public function mmTransactionPayment()
    {
        return $this->hasOne('\App\MmTransactionPayment', 'id_pembayaran', 'id_pembayaran');
    }
    
    public function mmItem()
    {
        return $this->hasOne('\App\MmItem', 'id_barang', 'id_barang');
    }
    
    public function mmPatientRegistration()
    {
        return $this->hasOne('\App\MmPatientRegistration', 'id_pendaftaran', 'id_pendaftaran');
    }
    
    public function mmPatient()
    {
        return $this->hasOne('\App\MmPatient', 'no_rekam_medis', 'no_rekam_medis');
    }
    
    public function mmDoctor()
    {
        return $this->hasOne('\App\MmDoctor', 'id_dokter', 'id_dokter');
    }
    
    public static function tipeRawatanLabels()
    {
        return [
            0 => 'Rawat Jalan',
            1 => 'Rawat Inap',
        ];
    }
    
    public function getTipeRawatanLabel()
    {
        $list = self::tipeRawatanLabels();
        return $list[$this->tipe_rawatan] ? $list[$this->tipe_rawatan] : $this->tipe_rawatan;
    }
    
    public function getFormattedMedicineDate()
    {
        return \Carbon\Carbon::parse($this->created_date)->format('d/m/y');
    }
    
    public function getNameAndAge()
    {
        $name = ($this->mmPatient) ? $this->mmPatient->nama : null;
        $age = ($this->mmPatient->tanggal_lahir) ? \Carbon\Carbon::parse($this->mmPatient->tanggal_lahir)->age : null;
        
        return $name . ' / ' . $age . 'th';
    }
    
    public function getName()
    {
        return ($this->mmPatient) ? $this->mmPatient->nama : null;
    }
    
    public function getDob()
    {
        return $age = ($this->mmPatient->tanggal_lahir) ? \Carbon\Carbon::parse($this->mmPatient->tanggal_lahir)->format('d/m/Y') : null;
    }
    
    public function getMedicineNameAndExp()
    {
        $name = ($this->mmItem) ? $this->mmItem->nama_barang : null;
        $quantity = ($this->mmItem) ? $this->mmItem->getFormattedItemExpiredAt() : null;;
        
        $resultName = $name;
        if (strlen($name) > 20) {
            $resultName = substr($name, 0, 20) . ' ...';
        }
        
        return $resultName . ' / ' . $quantity;
    }
    
    public function getItemSmallName()
    {
        if (isset($this->mmItem->mmItemSmall)) {
            return $this->mmItem->mmItemSmall->nama_satuan_kecil;
        }
        
        return $this->mmItem->id_barang_satuan_kecil;
    }
    
    public function getHowToUse()
    {
        $model = TransactionAddMedicineAdditionalDetail::where('transaction_medicine_id', $this->id_transaksi_obat)->first();
        if (!$model) {
            return null;
        }
        
        return $model->how_to_use;
    }
    
    public function getReceiptNumber()
    {
        $model = TransactionAddMedicineAdditionalDetail::where('transaction_medicine_id', $this->id_transaksi_obat)->first();
        if (!$model) {
            return $this->no_resep;
        }
        
        return $model->receipt_number;
    }
}
