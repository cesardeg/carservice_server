<?php


class ServiceDeliveryController extends Controller
{

	public function store($user_id, $service_diagnostic_id)
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
            $serviceDiagnostic = ServiceDiagnostic::find($service_diagnostic_id);
            if ($serviceDiagnostic === NULL)
            {
                $result['msj'] = 'No existe el diagnóstico número ' . $service_order_id;
                return Response::json($result);
            }
            if ($serviceDiagnostic->serviceOrder->workshop->id != $user->workshop->id)
            {
                 $result['msj'] = 'No tienes permiso de ver esté diagnóstico';
                return Response::json($result);
            }
			date_default_timezone_set('America/Mexico_City');
			$serviceDelivery = new ServiceDelivery();
			$serviceDelivery->user_id = $user_id;
			$serviceDelivery->service_diagnostic_id = $service_diagnostic_id;
			$serviceDelivery->date = date('Y-m-d H:i:s', time());
			$serviceDelivery->save();
			$result['success'] = true;
		}
		catch(Exception $e)
		{
			$result['msj'] = $e->getMessage();
		}
		return Response::json($result);
	}
}

?>