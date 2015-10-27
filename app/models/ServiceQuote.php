<?php

class ServiceQuote extends Eloquent {

    protected $fillable = array('status', 'service_order_id');

    public function serviceOrder()
    {
    	return $this->belongsTo('ServiceOrder');
    }

    public function user()
	{
		return $this->belongsTo('User');
	}

    public function updateTotal(){
        $this->subtotal = 0;
        foreach ($this->items as $item) {
            $this->subtotal += $item->subtotal;
        }
        $this->tax = $this->subtotal * 0.16;
        $this->total = $this->subtotal + $this->tax;
        $this->save();
    }

    public function items() {
        return $this->hasMany('ServiceQuoteItem');
    }
}

?>