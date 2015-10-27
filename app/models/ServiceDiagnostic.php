<?php

class ServiceDiagnostic extends Eloquent {

    protected $fillable = array('tires', 'front_shock_absorber', 'rear_shock_absorber', 'front_brakes', 'rear_brakes', 
        'suspension', 'bands', 'description', 'status', 'service_order_id');

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