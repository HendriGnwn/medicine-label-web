<?php

namespace App;

class TransactionAddMedicineAdditionalDetail extends BaseModel
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'transaction_add_medicine_additional_detail';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_medicine_id',
        'how_to_use',
        'receipt_number',
        'created_by',
        'updated_by',
    ];
    
    public function transactionAddMedicine()
    {
        return $this->hasOne('\App\TransactionAddMedicineAdditional', 'id', 'transaction_add_medicine_additional_id');
    }
}
