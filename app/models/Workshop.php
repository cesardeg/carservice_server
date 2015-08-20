<?php

class Workshop extends Eloquent 
{
    public function users()
    {
    	return $this->hasMany('User');
    }

    public function client()
    {
    	return $this->belongsTo('Client');
    }
}

?>