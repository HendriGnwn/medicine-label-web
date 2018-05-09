<?php

namespace App;

class TransactionAddMedicineAdditional extends BaseModel
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'transaction_add_medicine_additional';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_registration_id',
        'print_count',
        'created_by',
        'updated_by',
    ];
    
    public function mmTransactionAddMedicine()
    {
        return $this->hasOne('\App\MmTransactionAddMedicine', 'id_transaksi_obat', 'transaction_medicine_id');
    }
    
    public function transactionAddMedicineAdditionalDetails()
    {
        return $this->hasOne('\App\TransactionAddMedicineDetail', 'transaction_add_medicine_additional_id', 'id');
    }
    
    public function mmPatientRegistration()
    {
        return $this->hasOne('\App\MmPatientRegistration', 'id_pendaftaran', 'patient_registration_id');
    }
        
}
