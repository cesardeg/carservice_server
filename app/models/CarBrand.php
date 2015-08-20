<?php

class CarBrand extends Eloquent {

    protected $fillable = array('name');
    
    public function carLines()
    {
    	return $this->hasMany('CarLine');
    }
}

?>