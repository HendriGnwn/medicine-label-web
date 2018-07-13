<?php

namespace App;

class Pharmacist extends BaseModel
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'pharmacist';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'sik',
        'status',
        'created_at',
        'updated_at',
    ];
}
