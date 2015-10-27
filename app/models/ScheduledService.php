<?php

class ScheduledService extends Eloquent {

    protected $fillable = array('car_id', 'km', 'description');
    
    public function car()
    {
    	return $this->belongsTo('Car');
    }
}

?>