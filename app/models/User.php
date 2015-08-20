<?php

class User extends Eloquent {

    protected $fillable = array('firstname', 'lastname', 'mother_maiden_name', 'type');

    public function workshop()
    {
    	return $this->belongsTo('Workshop');
    }

    public function orderAsreceiverUser()
    {
    	return $this->hasMany('OrderService', 'receiver_user');
    }

    public function orderAsDeliverUser()
    {
    	return $this->hasMany('OrderService', 'deliver_user');
    }

}

?>