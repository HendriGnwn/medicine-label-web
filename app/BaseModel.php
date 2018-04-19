<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model {
    
    public function createdBy()
    {
        return $this->hasOne('\App\User', 'id', 'created_by');
    }
    
    public function updatedBy()
    {
        return $this->hasOne('\App\User', 'id', 'updated_by');
    }
    
    public function getCreatedName()
    {
        return ($this->createdBy) ? $this->createdBy->username : $this->created_by;
    }
    
    public function getUpdatedName()
    {
        return ($this->updatedBy) ? $this->updatedBy->username : $this->updated_by;
    }
}
