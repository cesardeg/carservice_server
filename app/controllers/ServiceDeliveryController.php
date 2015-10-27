<?php
use Carbon\Carbon;

class ServiceDeliveryController extends Controller
{
	public function store($user_id, $service_diagnostic_id)
	{
		$result = ['success' => false];
		try 
		{
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
			$serviceDelivery = new ServiceDelivery();
			$serviceDelivery->user_id = $user_id;
			$serviceDelivery->service_diagnostic_id = $service_diagnostic_id;
			$serviceDelivery->date = Carbon::now('America/Mexico_City');
			$serviceDelivery->save();
			$serviceDelivery->serviceDiagnostic->serviceOrder->car->car_status_id = CarStatus::WAITING_AGREE_DELIVERY;
			$serviceDelivery->serviceDiagnostic->serviceOrder->car->save();
            $data = [
                'car_owner_id' => $serviceDiagnostic->serviceOrder->car_owner_id,
                'type'         => NotificationType::REQUEST_AGREE_DELIVERY, 
                'title'        => 'Salida de vehículo',
                'message'      => 'Se ha registrado la salida de su vehículo de taller',
                'data'         => $serviceDelivery->id
            ];
            $notification = new Notification(array_merge($data, array('date' => Carbon::now('America/Mexico_City'))));
            $notification->save();
            Queue::push('SendNotification', $data);
			$result['success'] = true;
		}
		catch(Exception $e)
		{
			$result['msj'] = $e->getMessage();
		}
		return Response::json($result);
	}

    public function serviceDeliveryCarOwner($car_owner_id, $service_delivery_id)
    {
        $result = ['success' => false];
        $carOwner = CarOwner::find($car_owner_id);
        if ($carOwner === NULL)
        {
            $result['msj'] = 'No existe cliente';
            return Response::json($result);
        }
        $serviceDelivery = ServiceDelivery::find($service_delivery_id);
        if ($serviceDelivery === NULL)
        {
            $result['msj'] = 'No existe la salida de vehículo número ' . $service_delivery_id;
            return Response::json($result);
        }
        if ($serviceDelivery->serviceDiagnostic->serviceOrder->carOwner->id != $carOwner->id)
        {
             $result['msj'] = 'No tienes permiso de ver esta salida';
            return Response::json($result);
        }
        $result['service_delivery'] = [
            'status'        => $serviceDelivery->status()['description'],
            'user_in_charge'=> $serviceDelivery->user->first_name . ' ' . $serviceDelivery->user->last_name,
            'workshop_name' => $serviceDelivery->serviceDiagnostic->serviceOrder->workshop->name,
            'date'          => $serviceDelivery->date === NULL? 
                                   $serviceDelivery->date : 
                                   date_format(date_create($serviceDelivery->date), 'd-m-Y H:i'),
            'date_agree'    => $serviceDelivery->owner_agree === NULL? 
                                   $serviceDelivery->owner_agree : 
                                   date_format(date_create($serviceDelivery->owner_agree), 'd-m-Y H:i'),
            'date_disagree' => $serviceDelivery->owner_disagree === NULL? 
                                   $serviceDelivery->owner_disagree : 
                                   date_format(date_create($serviceDelivery->owner_disagree), 'd-m-Y H:i'),
            'date_cancel'   => $serviceDelivery->cancel === NULL? 
                                   $serviceDelivery->cancel : 
                                   date_format(date_create($serviceDelivery->diagnostic_cancel), 'd-m-Y H:i'),
            'car'           => $serviceDelivery->serviceDiagnostic->serviceOrder->car->brand . ' ' . 
                               $serviceDelivery->serviceDiagnostic->serviceOrder->car->model . ' ' . 
                               $serviceDelivery->serviceDiagnostic->serviceOrder->car->year

        ];
        $result['success'] = true;
        return Response::json($result);
    }

    public function serviceDeliveryAdmin($service_delivery_id)
    {
        $result = ['success' => false];
        try{
            if (Auth::user() === NULL)
            {
                $result['msj'] = 'Usuario no logeado';
                return Response::json($result);
            }
            $serviceDelivery = ServiceDelivery::find($service_delivery_id);
            if ($serviceDelivery === NULL)
            {
                $result['msj'] = 'No existe la salida de vehículo número ' . $service_delivery_id;
                return Response::json($result);
            }
            if ($serviceDelivery->serviceDiagnostic->serviceOrder->workshop_id != Auth::user()->workshop_id)
            {
                 $result['msj'] = 'No tienes permiso de ver esta salida';
                return Response::json($result);
            }
            $result['service_delivery'] = [
                'status'        => $serviceDelivery->status()['description'],
                'user_in_charge'=> $serviceDelivery->user->first_name . ' ' . $serviceDelivery->user->last_name,
                'date_created'  => $serviceDelivery->date === NULL? 
                                       $serviceDelivery->date : 
                                       date_format(date_create($serviceDelivery->date), 'd-m-Y H:i'),
                'date_agree'    => $serviceDelivery->owner_agree === NULL? 
                                       $serviceDelivery->owner_agree : 
                                       date_format(date_create($serviceDelivery->owner_agree), 'd-m-Y H:i'),
                'date_disagree' => $serviceDelivery->owner_disagree === NULL? 
                                       $serviceDelivery->owner_disagree : 
                                       date_format(date_create($serviceDelivery->owner_disagree), 'd-m-Y H:i'),
                'date_cancel'   => $serviceDelivery->cancel === NULL? 
                                       $serviceDelivery->cancel : 
                                       date_format(date_create($serviceDelivery->diagnostic_cancel), 'd-m-Y H:i')
            ];
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

	public function agree($car_owner_id, $service_delivery_id)
    {
        $result = ['success' => false];
        try {
            $carOwner = CarOwner::find($car_owner_id);
            if ($carOwner === NULL)
            {
                $result['msj'] = 'No existe el cliente';
                return Response::json($result);
            }
            $serviceDelivery = ServiceDelivery::find($service_delivery_id);
            if ($serviceDelivery === NULL)
            {
                $result['msj'] = 'No existe la salida de vehículo número ' . $service_delivery_id;
                return Response::json($result);
            }
            if ($serviceDelivery->serviceDiagnostic->serviceOrder->carOwner->id != $carOwner->id)
            {
                 $result['msj'] = 'No tienes permiso aceptar esta salida';
                return Response::json($result);
            }
            if ($serviceDelivery->owner_agree !== NULL)
            {
                $result['msj'] = 'Esta salida ya fué aceptada';
                return Response::json($result);
            }
            if ($serviceDelivery->owner_disagree !== NULL)
            {
                $result['msj'] = 'Esta salida ya fué rechazada';
                return Response::json($result);
            }
            if ($serviceDelivery->cancel !== NULL)
            {
                $result['msj'] = 'Esta salida ya fué cancelada';
                return Response::json($result);
            }
            $serviceDelivery->owner_agree = Carbon::now('America/Mexico_City');
            $serviceDelivery->save();
            $serviceDelivery->serviceDiagnostic->serviceOrder->car->car_status_id = CarStatus::IN_OPERATION;
            $serviceDelivery->serviceDiagnostic->serviceOrder->car->save();
            Notification::
                where('type', NotificationType::REQUEST_AGREE_DELIVERY)
                ->where('data', $service_delivery_id)
                ->update(array('active' => 0));
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        
        return Response::json($result);
    }

    public function disagree($car_owner_id, $service_delivery_id)
    {
        $result = ['success' => false];
        try {
            $carOwner = CarOwner::find($car_owner_id);
            if ($carOwner === NULL)
            {
                $result['msj'] = 'No existe el cliente';
                return Response::json($result);
            }
            $serviceDelivery = ServiceDelivery::find($service_delivery_id);
            if ($serviceDelivery === NULL)
            {
                $result['msj'] = 'No existe la salida de vehículo número ' . $service_delivery_id;
                return Response::json($result);
            }
            if ($serviceDelivery->serviceDiagnostic->serviceOrder->carOwner->id != $carOwner->id)
            {
                $result['msj'] = 'No tienes permiso aceptar esta salida';
                return Response::json($result);
            }
            if ($serviceDelivery === NULL)
            {
                $result['msj'] = 'No existe la salida de vehículo número ' . $service_delivery_id;
                return Response::json($result);
            }
            if ($serviceDelivery->owner_agree !== NULL)
            {
                $result['msj'] = 'Esta salida ya fué aceptada';
                return Response::json($result);
            }
            if ($serviceDelivery->owner_disagree !== NULL)
            {
                $result['msj'] = 'Esta salida ya fué rechazada';
                return Response::json($result);
            }
            if ($serviceDelivery->cancel !== NULL)
            {
                $result['msj'] = 'Esta salida ya fué cancelada';
                return Response::json($result);
            }
            $serviceDelivery->owner_disagree = Carbon::now('America/Mexico_City');
            $serviceDelivery->save();
            Notification::
                where('type', NotificationType::REQUEST_AGREE_DELIVERY)
                ->where('data', $service_delivery_id)
                ->update(array('active' => 0));
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function redo($service_delivery_id)
    {
        $result = ['success' => false];
        try {
            if (Auth::user() === NULL)
            {
                $result['msj'] = 'Usuario no autenticado';
                return Response::json($result);
            }
            $serviceDelivery = ServiceDelivery::find($service_delivery_id);
            if ($serviceDelivery === NULL)
            {
                $result['msj'] = 'No existe la salida de vehículo número ' . $service_delivery_id;
                return Response::json($result);
            }
            if ($serviceDelivery->user->workshop_id != Auth::user()->workshop_id)
            {
                $result['msj'] = 'No tienes permiso rehacer esta salida';
                return Response::json($result);
            }
            if ($serviceDelivery->owner_agree !== NULL)
            {
                $result['msj'] = 'Esta salida ya fué aceptada';
                return Response::json($result);
            }
            if ($serviceDelivery->cancel !== NULL)
            {
                $result['msj'] = 'Esta salida ya fué cancelada';
                return Response::json($result);
            }
            $serviceDelivery->delete();
            $serviceDelivery->serviceDiagnostic->serviceOrder->car->car_status_id = CarStatus::QUOTE_AGREED;
            $serviceDelivery->serviceDiagnostic->serviceOrder->car->save();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function cancel($service_delivery_id)
    {
        $result = ['success' => false];
        try {
            if (Auth::user() === NULL)
            {
                $result['msj'] = 'Usuario no autenticado';
                return Response::json($result);
            }
            $serviceDelivery = ServiceDelivery::find($service_delivery_id);
            if ($serviceDelivery === NULL)
            {
                $result['msj'] = 'No existe la salida de vehículo número ' . $service_delivery_id;
                return Response::json($result);
            }
            if ($serviceDelivery->user->workshop_id != Auth::user()->workshop_id)
            {
                $result['msj'] = 'No tienes permiso cancelar esta salida';
                return Response::json($result);
            }
            if ($serviceDelivery->owner_agree !== NULL)
            {
                $result['msj'] = 'Esta salida ya fué aceptada';
                return Response::json($result);
            }
            if ($serviceDelivery->cancel !== NULL)
            {
                $result['msj'] = 'Esta salida ya fué cancelada';
                return Response::json($result);
            }
            $serviceDelivery->cancel = Carbon::now('America/Mexico_City');
            $serviceDelivery->save();
            $serviceDelivery->serviceDiagnostic->serviceOrder->car->car_status_id = CarStatus::IN_OPERATION;
            $serviceDelivery->serviceDiagnostic->serviceOrder->car->save();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }
}

?>