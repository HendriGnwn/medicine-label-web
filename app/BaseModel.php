<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model {
    
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    /**
	 * @return array
	 */
    public static function statusLabels()
	{
		return [
			self::STATUS_ACTIVE => 'Active',
			self::STATUS_INACTIVE => 'Inactive',
		];
	}
	
	/**
	 * @return string
	 */
	public function getStatusLabel()
	{
		$list = self::statusLabels();
		
		return $list[$this->status] ? $list[$this->status] : $this->status;
	}
	
	/**
	 * @return boolean
	 */
	public function getIsStatusActive()
	{
		return $this->status == self::STATUS_ACTIVE;
	}
	
	/**
	 * @param type $query
	 * @return query
	 */
	public function scopeActived($query)
	{
		return $query->where($this->table . '.status', self::STATUS_ACTIVE);
	}
    
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
