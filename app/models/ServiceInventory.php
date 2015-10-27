<?php

class ServiceInventory extends Eloquent {


    protected $fillable = array('km', 'fuel_level', 'brake_fluid', 'wiper_fluid', 'antifreeze', 'oil', 'power_steering_fluid', 'status',  'service_order_id');

	public function photos()
	{
		return $this->hasMany('ServicePhoto');
	}

    public function serviceOrder()
    {
        return $this->belongsTo('ServiceOrder');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }
}

?>