<?php

class Car extends Eloquent {

    protected $fillable = array('epc', 'brand', 'model', 'year', 'serial_number', 'color', 'km', 
    	'notification_time', 'car_owner_id');

    public function carOwner()
    {
    	return $this->belongsTo('CarOwner');
    }

    public function serviceOrders()
    {
    	return $this->hasMany('ServiceOrder');
    }
}

?>