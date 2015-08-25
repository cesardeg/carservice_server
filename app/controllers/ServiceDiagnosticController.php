<?php


class ServiceDiagnosticController extends Controller
{

	public function store($user_id, $service_order_id)
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
			date_default_timezone_set('America/Mexico_City');
			$serviceDiagnostic = new ServiceDiagnostic();
			$serviceDiagnostic->user_id = $user_id;
			$serviceDiagnostic->service_order_id = $service_order_id;
			$serviceDiagnostic->date = date('Y-m-d H:i:s', time());
			$serviceDiagnostic->save();
			$result['success'] = true;
			$result['service_diagnostic_id'] = $serviceDiagnostic->id;
		}
		catch(Exception $e)
		{
			$result['msj'] = $e->getMessage();
		}
		return Response::json($result);
	}

	public function update($user_id, $service_diagnostic_id)
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
        	$serviceDiagnostic = ServiceDiagnostic::find($service_diagnostic_id);
        	if ($serviceDiagnostic === NULL)
	        {
	            $result['msj'] = 'No existe el diagnóstico número ' . $service_diagnostic_id;
	            return Response::json($result);
	        }
        	if ($serviceDiagnostic->serviceOrder->workshop->id != $user->workshop->id)
	        {
	        	 $result['msj'] = 'No tienes permiso de ver esté diagnóstico';
	            return Response::json($result);
	        }
	        if ($serviceDiagnostic->is_closed !== NULL)
	        {
	            $result['msj'] = 'Esté diagnóstico ya se encuentra cerrado, no se puede actualizar';
	            return Response::json($result);
	        }
	        $serviceDiagnostic->fill($input);
	        $serviceDiagnostic->save();
            ServiceQuoteItem::where('service_diagnostic_id', $service_diagnostic_id)->delete();
            foreach ($input['quote'] as $item) {
                $quoteItem = new ServiceQuoteItem($item);
                $quoteItem->service_diagnostic_id = $service_diagnostic_id;
                $quoteItem->save();
            }
	        $result['success'] = true;
        }
        catch (Exception $e) {
        	$result['msj'] = $e->getMessage();
        }
        return Response::json($result);
	}

	public function serviceDiagnostic($user_id, $service_diagnostic_id)
	{
		$result = ['success' => false];
		$user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
            return Response::json($result);
        }
        $serviceDiagnostic = ServiceDiagnostic::find($service_diagnostic_id);
        if ($serviceDiagnostic === NULL)
        {
            $result['msj'] = 'No existe el diagnóstico número ' . $service_diagnostic_id;
            return Response::json($result);
        }
        if ($serviceDiagnostic->serviceOrder->workshop->id != $user->workshop->id)
        {
        	 $result['msj'] = 'No tienes permiso de ver esté diagnóstico';
            return Response::json($result);
        }
        unset($serviceDiagnostic->serviceOrder);
        $serviceDiagnostic->quote;
        $result['success'] = true;
        $result['service_diagnostic'] = $serviceDiagnostic;
        return Response::json($result);
	}

	public function close($user_id, $service_diagnostic_id)
	{
		$result = ['success' => false];
		$user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
            return Response::json($result);
        }
        $serviceDiagnostic = ServiceDiagnostic::find($service_diagnostic_id);
        if ($serviceDiagnostic === NULL)
        {
            $result['msj'] = 'No existe el diagnóstico número ' . $service_diagnostic_id;
            return Response::json($result);
        }
        if ($serviceDiagnostic->serviceOrder->workshop->id != $user->workshop->id)
        {
        	 $result['msj'] = 'No tienes permiso de ver este diagnóstico';
            return Response::json($result);
        }
        if ($serviceDiagnostic->is_closed !== NULL)
        {
            $result['msj'] = 'Esté diagnóstico ya se encuentra cerrado';
            return Response::json($result);
        }
        date_default_timezone_set('America/Mexico_City');
        $serviceDiagnostic->is_closed = date('Y-m-d H:i:s', time());
        $serviceDiagnostic->save();
        $result['success'] = true;
        return Response::json($result);
	}

}

?>