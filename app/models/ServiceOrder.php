<?php

class ServiceOrder extends Eloquent {


    protected $fillable = array('car_id', 'workshop_id', 'receiver_user', 'service_name', 'total',
        'pick_up_address', 'delivery_address', 'owner_supplied_parts', 'owner_allow_used_parts');
    protected $guarded = array('id', 'date', 'owneer_agree', 'deliver_user');

	public function quote()
	{
		return $this->hasMany('ServiceQuoteItem');
	}

	public function photos()
	{
		return $this->hasMany('ServicePhoto');
	}

    public function diagnostic()
    {
    	return $this->hasOne('OrderDiagnostic');
    }

    public function car()
    {
    	return $this->belongsTo('Car');
    }

    public function workshop()
    {
    	return $this->belongsTo('Workshop');
    }

    public function receiverUser()
    {
    	return $this->belongsTo('User', 'receiver_user');
    }

    public function deliverUser()
    {
    	return $this->belongsTo('User', 'deliver_user');
    }
}

?>