<?php

class CarOwner extends Eloquent {
    protected $fillable = array('type', 'business_name', 'rfc', 'first_name', 'last_name', 'mother_maiden_name', 'street', 'neighborhood',
     'state_id', 'town_id', 'postal_code', 'email', 'phone_number', 'mobile_phone_number');

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

    public function notifications()
    {
        return $this->hasMany('Notification');
    }

    public function state()
    {
        return $this->belongsTo('State');
    }

    public function town()
    {
        return $this->belongsTo('Town');
    }

    public function tokens()
    {
        return $this->hasMany('Token');
    }

}
?>