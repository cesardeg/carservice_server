<?php


class CarController extends Controller
{

    public function store()
    {
        $input = Input::all();

        $result = ['success' => 0, 'msj' => ''];
        
        $car = new Car($input);
        try
        {
            $car->save();
            $result['success'] = 1;
            $result['id'] = $car->id;
        } catch (Exception $e)
        {
            if ($this->contains($e->getMessage(), '1062') &&
                $this->contains($e->getMessage(), 'key \'epc\'') )
            {
                $result['msj'] = 
                'Ya existe un vehículo con el mismo EPC, favor de verificar';
            }
            else if ($this->contains($e->getMessage(), '1062') &&
                     $this->contains($e->getMessage(), 'key \'serial_number\'') )
            {
                $result['msj'] = 
                'Ya existe un vehículo con el mismo número de serie, favor de verificar';
            }
            else
            {
                $result['msj'] = $e->getMessage();
            }
        }
        return Response::json($result);
    }
    
    public function findCarId($user_id, $epc)
    {
        $user = User::find($user_id);
        $result = ['success' => false];
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
        }
        else
        {
            $car = Car::select(array('id'))->where('epc', urldecode($epc))->get();
            if (count($car) > 0)
            {
                $result['success'] = true;
                $result['id'] = $car[0]->id;
            }
            else
            {
                $result['msj'] = 'Vehículo no encontrado';
            }
        }
        return Response::json($result);
    }

    public function carByEPC($client_id, $epc)
    {
        $car = Car::where('epc', str_replace('+', ' ', $epc))->get();
        if (count($car) > 0 && $car[0]->carOwner->client->id == $client_id)
        {
            if ($car[0]->carOwner->type == "Person")
            {
                $car[0]->ownerName = 
                    $car[0]->carOwner->first_name . ' ' . 
                    $car[0]->carOwner->last_name . ' ' . 
                    $car[0]->carOwner->mother_maiden_name;
            }
            else
            {
                $car[0]->ownerName = $car[0]->carOwner->business_name;
            }
            unset($car[0]->carOwner);

            return Response::json(['car' => $car[0]]);
        }
        return Response::json(['car' => NULL]);
    }
    
    function contains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }
}

?>