<?php

namespace App;

class TransactionMedicineDetail extends BaseModel
{
    const DRINK_NONE = 0;
    const DRINK_BEFORE_EATING = 1;
    const DRINK_AFTER_EATING = 2;
    
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
        'how_to_use',
        'drink',
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
    
    public function mmItem()
    {
        return $this->hasOne('\App\MmItem', 'id_barang', 'medicine_id');
    }
    
    public function getMedicineNameAndExp()
    {
        $name = ($this->mmItem) ? $this->mmItem->nama_barang : null;
        $quantity = $this->quantity;
        
        return $name;// . ' / ' . $quantity;
    }
    
    public function getItemSmallName()
    {
        if (isset($this->mmItem->mmItemSmall)) {
            return $this->mmItem->mmItemSmall->nama_satuan_kecil;
        }
        
        return $this->mmItem->id_barang_satuan_kecil;
    }
    
    public static function drinkLabels()
    {
        return [
            self::DRINK_NONE => 'None',
            self::DRINK_BEFORE_EATING => 'Sebelum makan',
            self::DRINK_AFTER_EATING => 'Sesudah makan',
        ];
    }
    
    public function getDrinkLabel()
    {
        $list = self::drinkLabels();
        if ($this->drink == self::DRINK_NONE) {
            return "&nbsp;";
        }
        return $list[$this->drink] ? $list[$this->drink] : $this->drink;
    }
}
