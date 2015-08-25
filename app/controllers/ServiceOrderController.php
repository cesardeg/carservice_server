<?php


class ServiceOrderController extends Controller
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
			$canCreate = $this->canCreateServiceOrder($user, $input['car_id']);
			if ( $canCreate !== true)
			{
				$result['msj'] = $canCreate;
				return Response::json($result);
			}
			date_default_timezone_set('America/Mexico_City');
			$serviceOrder = new ServiceOrder();
			$serviceOrder->user_id = $user_id;
			$serviceOrder->car_id = $input['car_id'];
			$serviceOrder->date = date('Y-m-d H:i:s', time());
			$serviceOrder->save();
			$serviceOrder->car_owner_id = $serviceOrder->car->carOwner->id;
			$serviceOrder->workshop_id = $serviceOrder->user->workshop->id;
			$serviceOrder->save();
			$result['success'] = true;
			$result['service_order_id'] = $serviceOrder->id;
		}
		catch(Exception $e)
		{
			$result['msj'] = $e->getMessage();
		}
		return Response::json($result);
	}

	public function canCreateServiceOrder($user, $car_id)
	{
		$car = Car::find($car_id);
        if ($car === NULL)
        {
            return false;
        }
		$lastService = $car->serviceOrders->map(
            function($serviceOrder) use ($user)
            {
                if ($serviceOrder->workshop->id == $user->workshop->id) 
                    return $serviceOrder;
            })->sortByDesc('date')->first();

        if ($lastService === NULL)
        {
            return true;
        }
        elseif ($lastService->is_closed === NULL)
        {
            return 'Ya hay una orden de servicio abierta';
        }
        elseif ($lastService->owner_agree === NULL)
        {
            return 'Ya hay una orden de servicio, esta esperando autorización del cliente';
        }
        elseif ($lastService->serviceDiagnostic === NULL)
        {
            return 'Ya hay una orden de servicio, esta esperando diagnóstico';
        }
        elseif ($lastService->serviceDiagnostic->is_closed === NULL)
        {
            return 'Ya hay una orden de servicio abierta, se está realizando un diagnostico';
        }
        elseif ($lastService->serviceDiagnostic->owner_agree === NULL)
        {
            return 'Ya hay una orden de servicio abierta, el diagnostico esta esperando autorizacion del cliente';
        }
        elseif ($lastService->serviceDiagnostic->serviceDeliver === NULL)
        {
            return 'Ya hay una orden de servicio abierta, el vehículo se encuentra en taller';
        }
        elseif ($lastService->serviceDiagnostic->serviceDeliver->owner_agree === NULL)
        {
            return 'Ya hay una orden de servicio abierta, se espera la autorizacion del cliente de salida';
        }
        else
        {
            return true;
        }
	}

	public function update($user_id, $service_order_id)
	{
		$result = ['success' => false];

		$user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
            return Response::json($result);
        }

        $input = Input::all();
        try 
        {
        	$serviceOrder = ServiceOrder::find($service_order_id);
        	if ($serviceOrder === NULL)
	        {
	            $result['msj'] = 'No existe la orden de servicio número ' . $service_order_id;
	            return Response::json($result);
	        }
        	if ($serviceOrder->workshop->id != $user->workshop->id)
	        {
	        	 $result['msj'] = 'No tienes permiso de ver esta orden de servicio';
	            return Response::json($result);
	        }
	        if ($serviceOrder->is_closed !== NULL)
	        {
	            $result['msj'] = 'Esta orden de servicio ya se encuentra cerrada, no se puede actualizar';
	            return Response::json($result);
	        }
	        $serviceOrder->fill($input);
	        $serviceOrder->save();
	        $result['success'] = true;
        }
        catch (Exception $e) {
        	$result['msj'] = $e->getMessage();
        }
        return Response::json($result);
	}

	public function serviceOrder($user_id, $service_order_id)
	{
		$result = ['success' => false];
		$user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
            return Response::json($result);
        }
        $serviceOrder = ServiceOrder::find($service_order_id);
        if ($serviceOrder === NULL)
        {
            $result['msj'] = 'No existe la orden de servicio número ' . $service_order_id;
            return Response::json($result);
        }
        if ($serviceOrder->workshop->id != $user->workshop->id)
        {
        	 $result['msj'] = 'No tienes permiso de ver esta orden de servicio';
            return Response::json($result);
        }
        unset($serviceOrder->workshop);
        $result['success'] = true;
        $result['service_oder'] = $serviceOrder;
        return Response::json($result);
	}

	public function close($user_id, $service_order_id)
	{
		$result = ['success' => false];
		$user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
            return Response::json($result);
        }
        $serviceOrder = ServiceOrder::find($service_order_id);
        if ($serviceOrder === NULL)
        {
            $result['msj'] = 'No existe la orden de servicio número ' . $service_order_id;
            return Response::json($result);
        }
        if ($serviceOrder->workshop->id != $user->workshop->id)
        {
        	 $result['msj'] = 'No tienes permiso de ver esta orden de servicio';
            return Response::json($result);
        }
        if ($serviceOrder->is_closed !== NULL)
        {
            $result['msj'] = 'Esta orden de servicio ya se encuentra cerrada';
            return Response::json($result);
        }
        date_default_timezone_set('America/Mexico_City');
        $serviceOrder->is_closed = date('Y-m-d H:i:s', time());
        $serviceOrder->save();
        $result['success'] = true;
        return Response::json($result);
	}

	/*public function store()
	{
		$input = Input::all();
		$result = ['success' => 0];
		date_default_timezone_set('America/Mexico_City');
		try 
		{
			$serviceOrder = new ServiceOrder($input);
			$serviceOrder->date = date('Y-m-d H:i:s', time());
			$serviceOrder->save();
			$service_order_id = $serviceOrder->id;
			$serviceDiagnostic = new ServiceDiagnostic($input['diagnostic']);
			$serviceDiagnostic->service_order_id = $service_order_id;
			$serviceDiagnostic->save();

			foreach ($input['quoteItems'] as $item) {
				$quoteItem = new ServiceQuoteItem($item);
				$quoteItem->service_order_id = $service_order_id;
				$quoteItem->save();
			}
			$result['success'] = 1;
		}catch(Exception $e)
		{
			return Response::json(['msj' => $e->getMessage()]);
		}
		return Response::json($result);
	}*/

}

?>