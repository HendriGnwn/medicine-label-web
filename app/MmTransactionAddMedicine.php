<?php

namespace App;

use Carbon\Carbon;

class MmTransactionAddMedicine extends BaseModel
{
    const CREATED_BY = User::CREATED_BY;
    
    const TYPE_IGD_JKN = 1;
    const TYPE_IGD_NON_PSIKIATRI = 2;
    const TYPE_IGD_PSIKIATRI = 3;
    const TYPE_IGD_TUNAI = 4;
    const TYPE_RAJAL_JKN = 5;
    const TYPE_RAJAL_NON_PSIKIATRI = 6;
    const TYPE_RAJAL_PSIKIATRI = 7;
    const TYPE_RANAP_NON_PSIKIATRI = 8;
    const TYPE_RANAP_PSIKIATRI = 9;
    
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
    
    public function mmUnit()
    {
        return $this->hasOne('\App\MmUnit', 'id_unit', 'id_unit');
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
    
    public function getDoctorName()
    {
        $name = $this->mmDoctor ? $this->mmDoctor->nama_dokter : '';
        if (strlen($name) > 25) {
            $name = substr($name, 0, 25) . ' ...';
        }
        return $name;
    }
    
    public function getFormattedMedicineDate()
    {
        $additionalData = json_decode($this->additional_data, true);
        $date = $additionalData['tanggal_order_obat'] ? $additionalData['tanggal_order_obat'] : $this->created_date;
        return Carbon::parse($date)->format('d/m/y');
    }
    
    public function getNameAndAge()
    {
        $name = ($this->mmPatient) ? $this->mmPatient->nama : null;
        $age = ($this->mmPatient->tanggal_lahir) ? Carbon::parse($this->mmPatient->tanggal_lahir)->age : null;
        
        return $name . ' / ' . $age . 'th';
    }
    
    public function getName()
    {
        $name = $this->mmPatient ? $this->mmPatient->nama : '';
        if (strlen($name) > 25) {
            $name = substr($name, 0, 25) . ' ...';
        }
        return $name;
    }
    
    public function getDob()
    {
        return $age = ($this->mmPatient->tanggal_lahir) ? Carbon::parse($this->mmPatient->tanggal_lahir)->format('d/m/Y') : null;
    }
    
    public function getMedicineNameAndExp()
    {
        $name = ($this->mmItem) ? $this->mmItem->nama_barang : null;
        $quantity = ($this->mmItem) ? $this->mmItem->getFormattedItemExpiredAt() : null;;
        
        $resultName = $name;
        if (strlen($name) > 25) {
            $resultName = substr($name, 0, 25) . ' ...';
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
    
    public function getArrayAdditionalData()
    {
        return json_decode($this->additional_data);
    }
    
    public function getHowToUse()
    {
        $model = TransactionAddMedicineAdditionalDetail::where('transaction_medicine_id', $this->id_transaksi_obat)->first();
        if (!$model) {
            return isset($this->getArrayAdditionalData()->aturan) ? $this->getArrayAdditionalData()->aturan : null;
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
    
    public static function deleteAllRecordOnBigData()
    {
        MmTransactionAddMedicine::where('created_by', MmTransactionAddMedicine::CREATED_BY)->delete();
        
        return true;
    }
    
    /**
     */
    public static function reportTransactionTypeLabels()
    {
        return [
            self::TYPE_IGD_JKN => 'IGD JKN',
            self::TYPE_IGD_NON_PSIKIATRI => 'IGD Non Psikiatri',
            self::TYPE_IGD_PSIKIATRI => 'IGD Psikiatri',
            self::TYPE_IGD_TUNAI => 'IGD Tunai',
            self::TYPE_RAJAL_JKN => 'Rajal JKN',
            self::TYPE_RAJAL_NON_PSIKIATRI => 'Rajal Non Psikiatri',
            self::TYPE_RAJAL_PSIKIATRI => 'Rajal Psikiatri',
            self::TYPE_RANAP_NON_PSIKIATRI => 'Ranap Non Psikiatri',
            self::TYPE_RANAP_PSIKIATRI => 'Ranap Psikiatri',
        ];
    }
    
    public function getCalculatePriceTotal()
    {
        $models = self::where('no_resep', $this->no_resep)
                ->where('id_pendaftaran', $this->id_pendaftaran)
                ->get();
        $total = 0;
        foreach ($models as $model) {
            $total += Helpers\NumberFormatter::ceiling($model->jml_permintaan * $model->harga);
        }
        return $total;
    }
    
    public function scopeTransactionType($query, $value)
    {
        
        switch($value) {
            case self::TYPE_IGD_JKN:
                return $query->leftJoin('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
                    ->where('mm_pasien_pendaftaran.jenis_pendaftaran', 4)
                    ->whereIn('mm_pasien_pendaftaran.id_jenis_pembayaran', [8,9]);
            case self::TYPE_IGD_NON_PSIKIATRI:
                return $query->leftJoin('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
                    ->where('mm_pasien_pendaftaran.jenis_pendaftaran', 4)
                    ->whereIn('mm_pasien_pendaftaran.id_jenis_pembayaran', [8,9]);
            case self::TYPE_IGD_PSIKIATRI:
                return $query->leftJoin('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
                    ->where('mm_pasien_pendaftaran.jenis_pendaftaran', 4)
                    ->where('mm_pasien_pendaftaran.id_unit', 25);
            case self::TYPE_IGD_TUNAI:
                return $query->leftJoin('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
                    ->where('mm_pasien_pendaftaran.jenis_pendaftaran', 4)
                    ->where('mm_pasien_pendaftaran.id_jenis_pembayaran', 0);
            case self::TYPE_RAJAL_JKN:
                return $query->leftJoin('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
                    ->where('mm_pasien_pendaftaran.jenis_pendaftaran', 1)
                    ->whereIn('mm_pasien_pendaftaran.id_jenis_pembayaran', [8,9]);
            case self::TYPE_RAJAL_NON_PSIKIATRI:
            case self::TYPE_RAJAL_PSIKIATRI:
            case self::TYPE_RANAP_NON_PSIKIATRI:
            case self::TYPE_RANAP_PSIKIATRI:
            default:
                return $query;
        }
    }
}
