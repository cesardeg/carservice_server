<?php

class CarStatus extends Eloquent {

    protected $fillable = array('description');

    const IN_OPERATION               = 0;
    const DOING_SERVICEORDER         = 1;
    const WAITING_AGREE_SERVICEORDER = 2;
    const SERVICEORDER_AGREED        = 3;
    const DOING_DIAGNOSTIC           = 4;
    const WAITING_AGREE_DIAGNOSTIC   = 5;
    const DIAGNOSTIC_AGREED          = 6;
    const DOING_QUOTE                = 7;
    const WAITING_AGREE_QUOTE        = 8;
    const QUOTE_AGREED               = 9;
    const WAITING_AGREE_DELIVERY     = 10;

    public function cars()
    {
    	return $this->hasMany('Car');
    }
}

?>