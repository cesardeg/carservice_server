<?php

class ServiceQuoteItem extends Eloquent {

	protected $fillable = array('description', 'amount', 'subtotal');
	protected $guarded = array('id');
	
    public function quote()
    {
    	return $this->belongsTo('ServiceQuote', 'service_quote_id');
    }

}

?>