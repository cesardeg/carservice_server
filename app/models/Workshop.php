<?php

class Workshop extends Eloquent 
{
    protected $fillable = array('name');
    public function users()
    {
    	return $this->hasMany('User');
    }

    public function serviceOrders()
    {
    	return $this->hasMany('ServiceOrder');
    }

    public function client()
    {
    	return $this->belongsTo('Client');
    }
}

?>