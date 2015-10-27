<?php

class ServiceStatus extends Eloquent {

    protected $fillable = array('description');
    public $timestamps = false;
    
    const DOES_NOT_APPLY = -1;
    const NO_AVAILABLE   =  0;
    const IN_PROCESS     =  1;
    const WAITING_AGREE  =  2;
    const AGREED         =  3;
    const DISAGREED      =  4;
}

?>