<?php

class ServiceDelivery extends Eloquent {

	protected $guarded = array('*');
	
    public function serviceDiagnostic()
    {
    	return $this->belongsTo('ServiceDiagnostic');
    }

    public function user()
    {
    	return $this->belongsTo('User');
    }
}

?>
