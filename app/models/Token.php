<?php

class Token extends Eloquent {
    protected $fillable = array('token', 'platform' ,'model');

    public function carOwner()
    {
    	return $this->belongsTo('CarOwner');
    }
}
?>