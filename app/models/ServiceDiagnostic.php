<?php

class ServiceDiagnostic extends Eloquent {

    protected $fillable = array('tires', 'front_shock_absorber', 'rear_shock_absorver', 'front_brakes', 'rear_brakes', 
        'suspension', 'bands', 'description', 'total');
    protected $guarded = array('id', 'date', 'is_closed', 'owner_agree', 'user_id', 'service_order_id');

    public function serviceOrder()
    {
    	return $this->belongsTo('ServiceOrder');
    }

    public function user()
	{
		return $this->belongsTo('User');
	}

    public function quote()
	{
		return $this->hasMany('ServiceQuoteItem');
	}

	public function serviceDeliver()
	{
		return $this->hasOne('ServiceDelivery');
	}

}

?>