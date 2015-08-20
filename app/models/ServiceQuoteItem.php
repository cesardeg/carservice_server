<?php

class ServiceQuoteItem extends Eloquent {

	protected $fillable = array('description', 'amount', 'subtotal');
	protected $guarded = array('id, service_order_id');
	
    public function serviceOrder()
    {
    	return $this->belongsTo('ServiceOrder');
    }

}

?>