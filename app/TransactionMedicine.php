<?php

namespace App;

class TransactionMedicine extends BaseModel
{
    const CARE_TYPE_OUTPATIENT = 0;
    const CARE_TYPE_INPATIENT = 1;
    
    const PAYMENT_DETAIL_STATUS_RUNNING = 0;
    const PAYMENT_DETAIL_STATUS_DONE = 1;
    const PAYMENT_DETAIL_STATUS_CANCEL = 2;
    const PAYMENT_DETAIL_STATUS_FIRST_CALLED = 3;
    const PAYMENT_DETAIL_STATUS_PROCCESS = 4;
    const PAYMENT_DETAIL_STATUS_RETUR = 5;
    
    const APPROVAL_STATUS_SAVE = 1;
    const APPROVAL_STATUS_APPROVE = 2;
    const APPROVAL_STATUS_CANCEL = 3;
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'transaction_medicine';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'doctor_id',
        'registered_id',
        'payment_id',
        'medical_record_number',
        'car_type',
        'payment_detail_status',
        'payment_detail_date',
        'approval_status',
        'created_at',
        'updated_at',
    ];
    
    public static function careTypeLabels()
    {
        return [
            self::CARE_TYPE_OUTPATIENT => 'Rawat Jalan',
            self::CARE_TYPE_INPATIENT => 'Rawat Inap',
        ];
    }
    
    public static function paymentDetaiLStatusLabels()
    {
        return [
            self::PAYMENT_DETAIL_STATUS_RUNNING => 'Berjalan',
            self::PAYMENT_DETAIL_STATUS_DONE => 'Selesai',
            self::PAYMENT_DETAIL_STATUS_CANCEL => 'Batal',
            self::PAYMENT_DETAIL_STATUS_FIRST_CALLED => 'Pertama dipanggil',
            self::PAYMENT_DETAIL_STATUS_PROCCESS => 'Proses',
            self::PAYMENT_DETAIL_STATUS_RETUR => 'Retur',
        ];
    }
    
    public static function approvalStatusLabels()
    {
        return [
            self::APPROVAL_STATUS_SAVE => 'Simpan',
            self::APPROVAL_STATUS_APPROVE => 'Approve',
            self::APPROVAL_STATUS_CANCEL => 'Batal',
        ];
    }
}
