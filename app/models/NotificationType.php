<?php

class NotificationType extends Eloquent {

    protected $fillable = array('description');
    public $timestamps = false;
    
    const REQUEST_AGREE_SERVICEORDER = 0;
    const REQUEST_AGREE_DIAGNOSTIC   = 1;
    const REQUEST_AGREE_QUOTE        = 2;
    const REQUEST_AGREE_DELIVERY     = 3;
    const REMIND_KMCAPTURE           = 4;
    const REMIND_SERVICETIME         = 5;

    public function notifications()
    {
    	return $this->hasMany('Notification');
    }
}

?>