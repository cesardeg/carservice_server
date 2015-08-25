<?php

class Client extends Eloquent {

	protected $fillable = array('name');
	
    public function workshops()
    {
    	return $this->hasMany('Workshop');
    }

    public function carOwners()
    {
    	return $this->hasMany('CarOwner');
    }

}

?>