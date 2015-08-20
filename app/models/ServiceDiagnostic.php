<?php

class ServiceDiagnostic extends Eloquent {

    protected $fillable = array('fuel_level', 'km', 'tires', 'front_shock_absorber', 'rear_shock_absorver', 'front_brakes', 'rear_brakes', 
        'suspension', 'bands', 'brake_fluid', 'wiper_fluid', 'antifreeze', 'oil', 'power_steering_fluid', 'description', 'mechanic_in_charge');
    protected $guarded = array('id, service_order_id');

    public function serviceOrder()
    {
    	return $this->belongsTo('ServiceOrder');
    }
}

?>