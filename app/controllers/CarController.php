<?php


class CarController extends Controller
{

    public function store($user_id)
    {
        $result = ['success' => false];
        $user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
            return Response::json($result);
        }
        try
        {
            $input = Input::all();
            $car = new Car($input);
            $car->save();
            $result['success'] = true;
            $result['id'] = $car->id;
        }
        catch (Exception $e)
        {
            if (contains($e->getMessage(), '1062') &&
                contains($e->getMessage(), 'key \'epc\'') )
            {
                $result['msj'] = 
                'Ya existe un vehículo con el mismo EPC, favor de verificar';
            }
            else if (contains($e->getMessage(), '1062') &&
                     contains($e->getMessage(), 'key \'serial_number\'') )
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
        $result = ['success' => false];

        $user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
            return Response::json($result);
        }
        $car = Car::select(array('id'))->where('epc', urldecode($epc))->get();
        if (count($car) > 0)
        {
            $result['success'] = true;
            $result['id'] = $car[0]->id;
            return Response::json($result);
        }

        $result['msj'] = 'Vehículo no encontrado';
        return Response::json($result);
    }

    public function car($user_id, $car_id)
    {
        $result = ['success' => false];

        $user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
            return Response::json($result);
        }
        $car = Car::find($car_id);
        if ($car === NULL)
        {
            $result['msj'] = 'Vehículo no encontrado';
            return $result;
        }
        if ($car->carOwner->type == "Person")
        {
            $car->ownerName = 
                $car->carOwner->first_name . ' ' . 
                $car->carOwner->last_name . ' ' . 
                $car->carOwner->mother_maiden_name;
        }
        else
        {
            $car->ownerName = $car->carOwner->business_name;
        }
        unset($car->carOwner);


        $result['car'] = $car;
        $result['success'] = true;

        #Last service done in the user's workshop
        $lastService = $car->serviceOrders->map(
            function($serviceOrder) use ($user)
            {
                if ($serviceOrder->workshop->id == $user->workshop->id) 
                    return $serviceOrder;
            })->sortByDesc('date')->first();

        if ($lastService === NULL)
        {
            $result['service_status'] = 0;
        }
        elseif ($lastService->is_closed === NULL)
        {
            $result['service_status'] = 1;
            $result['service_order_id'] = $lastService->id;
        }
        elseif ($lastService->owner_agree === NULL)
        {
            $result['service_status'] = 2;
        }
        elseif ($lastService->serviceDiagnostic === NULL)
        {
            $result['service_status'] = 3;
            $result['service_order_id'] = $lastService->id;
        }
        elseif ($lastService->serviceDiagnostic->is_closed === NULL)
        {
            $result['service_status'] = 4;
            $result['service_diagnostic_id'] = $lastService->serviceDiagnostic->id;
        }
        elseif ($lastService->serviceDiagnostic->owner_agree === NULL)
        {
            $result['service_status'] = 5;
        }
        elseif ($lastService->serviceDiagnostic->serviceDeliver === NULL)
        {
            $result['service_status'] = 6;
            $result['service_diagnostic_id'] = $lastService->serviceDiagnostic->id;
        }
        elseif ($lastService->serviceDiagnostic->serviceDeliver->owner_agree === NULL)
        {
            $result['service_status'] = 7;
        }
        else
        {
            $result['service_status'] = 0;
        }
        unset($car->serviceOrders);

        return Response::json($result);
    }    
    
}

function contains($haystack, $needle)
{
    return strpos($haystack, $needle) !== false;
}

?>