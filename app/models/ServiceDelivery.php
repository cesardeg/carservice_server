<?php

class ServiceDelivery extends Eloquent {

	protected $guarded = array('*');
	
    public function serviceDiagnostic()
    {
    	return $this->belongsTo('ServiceDiagnostic');
    }

    public function user()
    {
    	return $this->belongsTo('User');
    }

    public function status()
    {
        if ($this->owner_agree === NULL && $this->owner_disagree === NULL)
            return [
                'status'      => 2, 
                'description' => 'Esperando autorización',
                'id'          => $this->id
            ];
        if ($this->owner_agree !== NULL && $this->owner_disagree === NULL)
            return [
                'status'      => 3, 
                'description' => 'Aceptado',
                'id'          => $this->id
            ];
        if ($this->owner_disagree !== NULL && $this->cancel === NULL)
            return [
                'status'      => 4, 
                'description' => 'Rechazado',
                'id'          => $this->id
            ];
        if ($this->cancel !== NULL)
            return [
                'status'      => 5, 
                'description' => 'Cancelado',
                'id'          => $this->id
            ];
        
        return [
            'status'      => -1, 
            'description' => 'Desconocido', 
            'id'          => $this->id
        ];
    }
}

?>
