<?php

class ServicePhoto extends Eloquent {

    protected $fillable = array('service_order_id', 'type', 'photo');
    protected $guarded = array('id'):

    public function serviceOrder()
    {
    	return $this->belongsTo('ServiceOrder');
    }
}

?>