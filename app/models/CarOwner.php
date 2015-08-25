<?php

class CarOwner extends Eloquent {
    protected $fillable = array('type', 'business_name', 'rfc', 'firstname', 'lastname', 'mother_maiden_name', 'street', 'neighborhood',
     'state', 'town', 'postal_code', 'email', 'phone_number', 'mobile_phone_number', 'username', 'client_id');

    public function cars()
    {
    	return $this->hasMany('Car');
    }

    public function client()
    {
    	return $this->belongsTo('Client');
    }

    public function serviceOrders()
    {
        return $this->hasMany('ServiceOrder');
    }

}
?>