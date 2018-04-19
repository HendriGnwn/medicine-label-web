<?php

namespace App;

class TransactionMedicine extends BaseModel
{
    const CARE_TYPE_OUTPATIENT = 0;
    const CARE_TYPE_INPATIENT = 1;
    
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
        'medical_record_number',
        'care_type',
        'medicine_date',
        'receipt_number',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];
    
    public static function careTypeLabels()
    {
        return [
            self::CARE_TYPE_OUTPATIENT => 'Rawat Jalan',
            self::CARE_TYPE_INPATIENT => 'Rawat Inap',
        ];
    }
    
    public function getCareTypeLabel()
    {
        $list = self::careTypeLabels();
        return $list[$this->care_type] ? $list[$this->care_type] : $this->care_type;
    }
    
    public function mmDoctor()
    {
        return $this->hasOne('\App\MmDoctor', 'id_dokter', 'doctor_id');
    }
    
    public function mmPatient()
    {
        return $this->hasOne('App\MmPatient', 'no_rekam_medis', 'medical_record_number');
    }
    
    public function transactionMedicineDetail()
    {
        return $this->hasMany('App\TransactionMedicineDetail', 'transaction_medicine_id', 'id');
    }
    
    public function getFormattedMedicineDate()
    {
        return \Carbon\Carbon::parse($this->medicine_date)->format('d/m/y');
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
}
