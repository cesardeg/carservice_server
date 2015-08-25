<?php

class Town extends Eloquent {
    
    public $timestamps = false;
    public function state()
    {
    	return $this->hasMany('State');
    }
}

?>