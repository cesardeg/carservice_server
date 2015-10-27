<?php

class Notification extends Eloquent {

    protected $fillable = array('car_owner_id', 'type', 'date', 'title', 'message', 'data', 'active');
    
    public function carOwner()
    {
    	return $this->belongsTo('CarOwner');
    }

    public function type()
    {
    	return $this->belongsTo('NotificationType');
    }
}

?>