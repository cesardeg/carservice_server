<?php

class Car extends Eloquent {

    protected $fillable = array('tag', 'brand', 'model', 'year', 'serial_number', 'color', 'km', 'license_plate',
    	'notification_time', 'car_owner_id');

    public function carOwner()
    {
    	return $this->belongsTo('CarOwner');
    }

    public function serviceOrders()
    {
    	return $this->hasMany('ServiceOrder');
    }

    public function currentServiceOrder()
    {
        return $this->belongsTo('ServiceOrder', 'service_order_id');
    }
    
    public function reminders()
    {
        return $this->hasMany('Reminder');
    }

    public function scheduledServices()
    {
        return $this->hasMany('ScheduledService');
    }
    
    public function status()
    {
        return $this->belongsTo('CarStatus', 'car_status_id');
    }
}

?>