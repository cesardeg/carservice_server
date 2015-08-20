<?php

class Client extends Eloquent {


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