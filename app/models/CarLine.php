<?php

class CarLine extends Eloquent {
    protected $fillable = array('name', 'car_brand_id');

    public function carBrand()
    {
    	return $this->belongsTo('CarBrand');
    }
}

?>