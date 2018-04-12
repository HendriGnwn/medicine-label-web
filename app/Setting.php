<?php

namespace App;

class Setting extends BaseModel
{
    const SETTING_FIRST = 1;
    
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'setting';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address',
        'apoteker',
        'sik',
        'created_at',
        'updated_at',
    ];
}
