<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class TransactionMedicine extends BaseModel
{
    use SoftDeletes;

    const IS_POST_TO_DB_TRUE = 1;
    const IS_POST_TO_DB_FALSE = 0;
    
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
        'unit_id',
        'registered_id',
        'medical_record_number',
        'care_type',
        'medicine_date',
        'receipt_number',
        'is_post_to_db',
        'deleted_at',
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
    
    public function mmUnit()
    {
        return $this->hasOne('\App\MmUnit', 'id_unit', 'unit_id');
    }
    
    public function mmPatient()
    {
        return $this->hasOne('App\MmPatient', 'no_rekam_medis', 'medical_record_number');
    }
    
    public function transactionMedicineDetails()
    {
        return $this->hasMany('App\TransactionMedicineDetail', 'transaction_medicine_id', 'id');
    }
    
    public function mmPatientRegistration()
    {
        return $this->hasOne('App\MmPatientRegistration', 'id_pendaftaran', 'registered_id');
    }
    
    public function mmTransactionPayment()
    {
        return $this->hasOne('App\MmTransactionPayment', 'id_pendaftaran', 'registered_id');   
    }
    
    public function getFormattedMedicineDate()
    {
        return Carbon::parse($this->medicine_date)->format('d/m/y');
    }
    
    public function getNameAndAge()
    {
        $name = ($this->mmPatient) ? $this->mmPatient->nama : null;
        $age = ($this->mmPatient->tanggal_lahir) ? Carbon::parse($this->mmPatient->tanggal_lahir)->age : null;
        
        return $name . ' / ' . $age . 'th';
    }
    
    public function getDoctorName()
    {
        $name = $this->mmDoctor ? $this->mmDoctor->nama_dokter : '';
        if (strlen($name) > 25) {
            $name = substr($name, 0, 25) . ' ...';
        }
        return $name;
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
    
    /**
     * @return boolean
     */
    public function storeToBigDatabaseSimrs()
    {
        Log::useDailyFiles(storage_path() . '/logs/console-push-to-bigdata.log');
        Log::info('Store Process is begin');
        \DB::beginTransaction();
        foreach ($this->transactionMedicineDetails as $detail) :
            $transaction = new MmTransactionAddMedicine();
            $transaction->id_pembayaran = $this->mmTransactionPayment ? $this->mmTransactionPayment->id_pembayaran : null;
            $transaction->id_pendaftaran = $this->registered_id;
            $transaction->id_dokter = $this->doctor_id;
            $transaction->id_unit = $this->unit_id;
            $transaction->id_barang = $detail->medicine_id;
            $transaction->no_rekam_medis = $this->medical_record_number;
            $transaction->status_detail_pembayaran = $this->mmTransactionPayment ? $this->mmTransactionPayment->status_pembayaran : null;
            $transaction->tanggal_detail_pembayaran = $this->mmTransactionPayment ? $this->mmTransactionPayment->tanggal_pembayaran : null;
            $transaction->tipe_rawatan = $this->care_type;
            $transaction->status_approve = 2;
            $transaction->jml_permintaan = $detail->quantity;
            $transaction->harga = $detail->mmItem ? $detail->mmItem->harga_jual : 0;
            $transaction->no_resep = $this->receipt_number;
            $transaction->no_transaksi = null;
            $transaction->is_generate = 0;
            $transaction->additional_data = json_encode([
                'kode_obat' => $detail->medicine_id,
                'nama_obat' => null,
                'nama_obat_r' => null,
                'aturan' => $detail->how_to_use,
                'sediaan' => null,
                'jml_permintaan' => $this->quantity,
                'pelayanan' => null,
                'qty_pelayanan' => null,
                'lapkemenkes' => null,
                'laplain' => null,
                'sub_total' => ($detail->mmItem ? $detail->mmItem->harga_jual : 0) * (int)$detail->quantity,
                'racikan' => null,
                'status_racikan' => null,
                'harga' => $detail->mmItem ? $detail->mmItem->harga_jual : 0,
                'qty_retur' => null,
                'harga_retur' => null,
                'total_retur' => null,
                'id_obat_order' => null,
                'id_sediaan' => null,
                'tanggal_order_obat' => null,
            ]);
            $transaction->created_date = Carbon::now()->toDateTimeString();
            $transaction->created_by = MmTransactionAddMedicine::CREATED_BY;
            $transaction->modified_count = 0;
            $transaction->last_modified_date = null;
            $transaction->last_modified_by = null;
            $transaction->is_deleted = 0;
            $transaction->deleted_date = null;
            $transaction->deleted_by = null;
            $transaction->save();

            $detail->mm_transaction_add_medicine_id = $transaction->id_transaksi_obat;
            $detail->save();

            $this->is_post_to_db = self::IS_POST_TO_DB_TRUE;
            $this->post_to_db_at = Carbon::now()->toDateTimeString();
            $this->save();

            Log::info('Data Successfully saved.');
            Log::info($transaction->toJson());

        endforeach;
        \DB::commit();
        
        Log::info('Store Process is end');
        return true;
    }
    
    /**
     * @return boolean
     */
    public function updateToBigDatabaseSimrs()
    {
        Log::useDailyFiles(storage_path() . '/logs/console-push-to-bigdata.log');
        Log::info('Update Process is begin');
        \DB::beginTransaction();
        foreach ($this->transactionMedicineDetails as $detail) :
            $transaction = MmTransactionAddMedicine::where('id_transaksi_obat', $detail->mm_transaction_add_medicine_id)->first();
            if (!$transaction) {
                $transaction = new MmTransactionAddMedicine();
            }
            $transaction->id_pembayaran = $this->mmTransactionPayment ? $this->mmTransactionPayment->id_pembayaran : null;
            $transaction->id_pendaftaran = $this->registered_id;
            $transaction->id_dokter = $this->doctor_id;
            $transaction->id_unit = $this->unit_id;
            $transaction->id_barang = $detail->medicine_id;
            $transaction->no_rekam_medis = $this->medical_record_number;
            $transaction->status_detail_pembayaran = $this->mmTransactionPayment ? $this->mmTransactionPayment->status_pembayaran : null;
            $transaction->tanggal_detail_pembayaran = $this->mmTransactionPayment ? $this->mmTransactionPayment->tanggal_pembayaran : null;
            $transaction->tipe_rawatan = $this->care_type;
            $transaction->status_approve = 2;
            $transaction->jml_permintaan = $detail->quantity;
            $transaction->harga = $detail->mmItem ? $detail->mmItem->harga_jual : 0;
            $transaction->no_resep = $this->receipt_number;
            $transaction->no_transaksi = null;
            $transaction->is_generate = 0;
            $transaction->additional_data = json_encode([
                'kode_obat' => $detail->medicine_id,
                'nama_obat' => null,
                'nama_obat_r' => null,
                'aturan' => $detail->how_to_use,
                'sediaan' => null,
                'jml_permintaan' => $this->quantity,
                'pelayanan' => null,
                'qty_pelayanan' => null,
                'lapkemenkes' => null,
                'laplain' => null,
                'sub_total' => ($detail->mmItem ? $detail->mmItem->harga_jual : 0) * (int)$detail->quantity,
                'racikan' => null,
                'status_racikan' => null,
                'harga' => $detail->mmItem ? $detail->mmItem->harga_jual : 0,
                'qty_retur' => null,
                'harga_retur' => null,
                'total_retur' => null,
                'id_obat_order' => null,
                'id_sediaan' => null,
                'tanggal_order_obat' => null,
            ]);
            $transaction->created_date = Carbon::now()->toDateTimeString();
            $transaction->created_by = 'sistemlabelingobat';
            $transaction->modified_count = 0;
            $transaction->last_modified_date = null;
            $transaction->last_modified_by = null;
            $transaction->is_deleted = 0;
            $transaction->deleted_date = null;
            $transaction->deleted_by = null;
            $transaction->save();

            $detail->mm_transaction_add_medicine_id = $transaction->id_transaksi_obat;
            $detail->save();

            $this->is_post_to_db = self::IS_POST_TO_DB_TRUE;
            $this->post_to_db_at = Carbon::now()->toDateTimeString();
            $this->save();

            Log::info('Data Successfully updated.');
            Log::info($transaction->toJson());

        endforeach;
        \DB::commit();
        
        Log::info('Update Process is end');
        return true;
    }
    
    /**
     * @param type $padLength
     * @return type
     */
    public static function generateReceiptNumber($padLength = 4)
    {
		$left = date('ymd');
        $leftLen = strlen($left);
        $increment = 1;
        
        $last = MmTransactionAddMedicine::where('no_resep', 'like', "%$left%")
            ->orderBy('id_transaksi_obat', 'desc')
            ->limit(1)
            ->first();

        if ($last) {
            $increment = (int) substr($last->no_resep, $leftLen, $padLength);
            $increment++;
        }

        $number = str_pad($increment, $padLength, '0', STR_PAD_LEFT);

        return $left . $number;
    }
}
