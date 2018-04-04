<?php

namespace App;

class TransactionMedicineDetail extends BaseModel
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'transaction_medicine_detail';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_medicine_id',
        'unit_id',
        'medicine_id',
        'name',
        'quantity',
        'price',
        'receipt_number',
        'trx_number',
        'data',
        'created_at',
        'updated_at',
    ];
    
    public function transactionMedicine()
    {
        return $this->hasOne('\App\TransactionMedicine', 'id', 'transaction_medicine_id');
    }
}
