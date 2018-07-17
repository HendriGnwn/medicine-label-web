<?php

namespace App;

class MmPatientRegistration extends BaseModel
{
    const REGISTER_TYPE_IGD = 4;
    
    protected $connection = 'mysqlMm';
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'mm_pasien_pendaftaran';
    
    protected $primaryKey = 'id_pendaftaran';
    
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no_pendaftaran',
        'no_antrian',
        'no_urut_harian',
        'no_rekam_medis',
        'tanggal_pendaftaran',
        'id_dokter',
        'id_dokter_gizi',
        'id_unit',
        'id_rujuk',
        'id_jenis_pembayaran',
        'id_shift',
        'no_asuransi',
        'status_pasien',
        'status_rujuk',
        'status_daftar',
        'id_status_keluar',
        'no_sjp',
        'no_sep',
        'kelas_perawatan',
        'alamat_pendaftaran',
        'jenis_pendaftaran',
        'is_aps',
        'data_igd',
        'lahir',
        'umur',
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
    
    public function mmTransactionAddMedicines()
    {
        return $this->hasMany('\App\MmTransactionAddMedicine', 'id_pendaftaran', 'id_pendaftaran');
    }
    
    public function mmTransactionAddMedicine()
    {
        return $this->hasOne('\App\MmTransactionAddMedicine', 'id_pendaftaran', 'id_pendaftaran');
    }
    
    public function mmItem()
    {
        return $this->hasOne('\App\MmItem', 'id_barang', 'id_barang');
    }
    
    public function mmPatient()
    {
        return $this->hasOne('\App\MmPatient', 'no_rekam_medis', 'no_rekam_medis');
    }
    
    public function mmDoctor()
    {
        return $this->hasOne('\App\MmDoctor', 'id_dokter', 'id_dokter');
    }
    
    public function mmUnit()
    {
        return $this->hasOne('\App\MmUnit', 'id_unit', 'id_unit');
    }
    
    public function transactionAddMedicineAdditional()
    {
        return $this->hasOne('\App\TransactionAddMedicineAdditional', 'patient_registration_id', 'id_pendaftaran');
    }
    
    public function getItemQuantity($itemId, $receiptNumber)
    {
        $medicine = $this->mmTransactionAddMedicines()->where('id_barang', $itemId)->where('no_resep', $receiptNumber)->first();
        if (!$medicine) {
            return null;
        }
        
        return $medicine->jml_permintaan;
    }
}
