<?php

class State extends Eloquent {
    public $timestamps = false;

    public function towns()
    {
    	return $this->hasMany('Town');
    }
}

?>