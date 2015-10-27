<?php

class ServicePhoto extends Eloquent {

    protected $fillable = array('type', 'photo', 'service_inventory_id');
    protected $guarded  = array('id');

    public function inventory()
    {
    	return $this->belongsTo('ServiceInventory');
    }
}

?>