<?php

class Reminder extends Eloquent {

    protected $fillable = array('car_id', 'subject', 'remind', 'frequency', 'time_unit', 'next_reminder');
    protected $guarded = array('id');

    public function car()
    {
    	return $this->belongsTo('Car');
    }

}

?>