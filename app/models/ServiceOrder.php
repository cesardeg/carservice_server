<?php

class ServiceOrder extends Eloquent {


    protected $fillable = array('service_name', 'pick_up_address', 'delivery_address', 'owner_supplied_parts', 'owner_allow_used_parts', 
                                'km', 'fuel_level', 'brake_fluid', 'wiper_fluid', 'antifreeze', 'oil', 'power_steering_fluid');
    protected $guarded = array('id', 'user_id', 'car_id', 'date', 'is_closed', 'owneer_agree', 'workshop_id', 'car_owner_id');


	public function photos()
	{
		return $this->hasMany('ServicePhoto');
	}

    public function serviceDiagnostic()
    {
    	return $this->hasOne('ServiceDiagnostic');
    }

    public function car()
    {
    	return $this->belongsTo('Car');
    }

    public function workshop()
    {
    	return $this->belongsTo('Workshop');
    }

    public function user()
    {
    	return $this->belongsTo('User');
    }

    public function carOwner()
    {
        return $this->belongsTo('CarOwner');
    }

    public function delivery()
    {
        return  $this->hasManyThrough('ServiceDelivery', 'ServiceDiagnostic');
    }

}

?>