<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class User extends Eloquent implements UserInterface, RemindableInterface{

    use UserTrait, RemindableTrait;
    use SoftDeletingTrait;

    protected $fillable = array('first_name', 'last_name', 'mother_maiden_name', 'sex', 'birthdate', 'address', 'neighborhood', 'city', 'state_id', 'postal_code', 'cell_phone', 'home_phone', 'email');


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

    public function serviceOrders()
    {
        return $this->hasMany('ServiceOrder');
    }

    public function serviceDiagnostics()
    {
        return $this->hasMany('ServiceDiagnostic');
    }

    public function serviceDeliveries()
    {
        return $this->hasMany('ServiceDelivery');
    }

}

?>