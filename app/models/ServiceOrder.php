<?php

class ServiceOrder extends Eloquent {


    protected $fillable = array('service_name', 'pick_up_address', 'delivery_address', 'owner_supplied_parts', 'owner_allow_used_parts');


    public function inventory()
    {
        return $this->hasOne('ServiceInventory');
    }

    public function diagnostic()
    {
    	return $this->hasOne('ServiceDiagnostic');
    }

    public function quote()
    {
        return $this->hasOne('ServiceQuote');
    }

    public function car()
    {
    	return $this->belongsTo('Car');
    }

    public function carOwner()
    {
        return $this->belongsTo('CarOwner');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function workshop()
    {
    	return $this->belongsTo('Workshop');
    }

}

?>