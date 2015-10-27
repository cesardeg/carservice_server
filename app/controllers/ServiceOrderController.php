<?php
use Carbon\Carbon;

class ServiceOrderController extends Controller
{
	public function store($user_id)
	{
		$result = ['success' => false];
		try {
            $user = User::find($user_id);
            if ($user === NULL)
            {
                $result['msj'] = 'No ha iniciado sesión el usuario';
                return Response::json($result);
            }
			$input = Input::all();
            $car = Car::find($input['car_id']);
            if ($car === NULL)
            {
                $result['msj'] = "No existe el vehículo";
                return Response::json($result);
            }
            if ($car->service_order_id)
            {
                $result['msj'] = "Vehículo ya se encuentra en taller";
                return Response::json($result);
            }
			$serviceOrder = new ServiceOrder($input);
			$serviceOrder->car_id = $input['car_id'];
			$serviceOrder->car_owner_id = $car->car_owner_id;
            $serviceOrder->user_id = $user_id;
			$serviceOrder->workshop_id = $user->workshop_id;
            $serviceOrder->entry_date = Carbon::now('America/Mexico_City');
			$serviceOrder->save();
            $car->service_order_id = $serviceOrder->id;
            $car->save();
            $data = ['service_order_id' => $serviceOrder->id];
            (new ServiceInventory (array_merge($data, ['status' => ServiceStatus::IN_PROCESS  ])))->save();
            (new ServiceDiagnostic(array_merge($data, ['status' => ServiceStatus::NO_AVAILABLE])))->save();
            (new ServiceQuote     (array_merge($data, ['status' => ServiceStatus::NO_AVAILABLE])))->save();
            Notification::where('car_owner_id', $serviceOrder->car_owner_id)
            ->where('data', $serviceOrder->car_id)
            ->where('active', 1)
            ->where('type', NotificationType::REMIND_KMCAPTURE)
            ->update(array('active' => 0));
            Notification::where('car_owner_id', $serviceOrder->car_owner_id)
            ->where('active', 1)
            ->where('type'  , NotificationType::REMIND_SERVICETIME)->get()
            ->map(function($notification) use($car){
                $scheduledService = ScheduledService::find($notification->data);
                if ($scheduledService->car_id == $car->id)
                {
                    $notification->active = 0;
                    $notification->save();
                } 
            });
            $result['success'] = true;
		} catch(Exception $e) {
			$result['msj'] = $e->getMessage();
		}
		return Response::json($result);
	}

    public function completeService($user_id, $service_order_id)
    {
        $result = ['success' => false];
        try {
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
            if ($serviceOrder->workshop_id != $user->workshop_id)
            {
                $result['msj'] = 'No tienes permiso de ver esta orden de servicio';
                return Response::json($result);
            }
            if (!($serviceOrder->inventory ->status == ServiceStatus::AGREED && 
                  $serviceOrder->diagnostic->status == ServiceStatus::AGREED &&
                  $serviceOrder->quote     ->status == ServiceStatus::AGREED))
            {
                $result['msj'] = 'al menos una fase no autorizada, no se puede terminar servicio';
                return Response::json($result);
            }
            if ($serviceOrder->completion_date)
            {
                $result['msj'] = 'servicio ya terminado';
                return Response::json($result);
            }
            $serviceOrder->completion_date = Carbon::now('America/Mexico_City');
            $serviceOrder->save();
            $data = [
                'car_owner_id' => $serviceOrder->car_owner_id,
                'type'         => NotificationType::REQUEST_AGREE_DELIVERY, 
                'title'        => 'Sevicio terminado',
                'message'      => 'El servicio técnico de su vehículo ha finalizado',
                'data'         => $service_order_id
            ];
            $notification = new Notification(array_merge($data, array('date' => Carbon::now('America/Mexico_City'))));
            $notification->save();
            Queue::push('SendNotification', $data);
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function carsInWorkshop()
    {
        $services  = [];
        try {
            $services = Car::
                whereNotNull('service_order_id')
                ->get()->map(function($car){
                    return $car->currentServiceOrder;
                })->filter(function($serviceOrder){
                    return $serviceOrder->workshop_id == Auth::user()->workshop_id;
                })->sortByDesc('entry_date')
                  ->map(function($serviceOrder){
                    return [
                        'service_order_id'  => $serviceOrder->id,
                        'entry_date'        =>  date_format(date_create($serviceOrder->entry_date), 'd-m-Y H:i'),
                        'completion_date'   =>  $serviceOrder->completion_date ? date_format(date_create($serviceOrder->completion_date ), 'd-m-Y H:i') : NULL,
                        'exit_date'         =>  $serviceOrder->exit_date       ? date_format(date_create($serviceOrder->exit_date       ), 'd-m-Y H:i') : NULL,
                        'car'               =>  [
                                                    'id'          => $serviceOrder->car->id,
                                                    'description' => $serviceOrder->car->brand . ' ' . $serviceOrder->car->model . ' ' . $serviceOrder->car->year
                                                ],
                        'owner'             =>  [
                                                    'id'   => $serviceOrder->carOwner->id,
                                                    'name' => $serviceOrder->carOwner->type == 'Business' ? 
                                                    $serviceOrder->carOwner->business_name :
                                                    $serviceOrder->carOwner->first_name . ' ' . $serviceOrder->carOwner->last_name . ' ' . $serviceOrder->carOwner->mother_maiden_name
                                                ],
                        'order_status'      =>  $serviceOrder->inventory->status,
                        'diagnostic_status' =>  $serviceOrder->diagnostic->status,
                        'quote_status'      =>  $serviceOrder->quote->status,
                    ];
                });
        } catch (Exception $e) {
            
        }   
        //return Response::json($services);
        return View::make('cars.record_list',['version'=>'workshop','services'=>$services]);         
    }

    public function workshopHistory()
    {
        $services  = [];
        $input = Input::all();
        $ini_date = Carbon::now('America/Mexico_City')->subMonth();
        $fin_date = Carbon::now('America/Mexico_City');
        if (isset($input['ini_date']))
            if ($input['ini_date'] != '')
                $ini_date = new Carbon($input['ini_date']);
            else
                $ini_date = new Carbon('@'. 0);
        if (isset($input['fin_date']) && $input['fin_date'] != '')
            $fin_date = (new Carbon($input['fin_date']))->addDay()->subSecond();

        try {

            $services = ServiceOrder::
                where('workshop_id', Auth::user()->workshop_id)
                ->whereBetween('entry_date', array($ini_date, $fin_date))->get()
                ->filter(function($serviceOrder) {
                    return $serviceOrder->exit_date ? true : false;
                })->sortByDesc('entry_date')
                ->map(function($serviceOrder){
                    return [
                        'service_order_id'  => $serviceOrder->id,
                        'entry_date'        =>  date_format(date_create($serviceOrder->entry_date), 'd-m-Y H:i'),
                        'completion_date'   =>  $serviceOrder->completion_date ? date_format(date_create($serviceOrder->completion_date ), 'd-m-Y H:i') : NULL,
                        'exit_date'         =>  $serviceOrder->exit_date       ? date_format(date_create($serviceOrder->exit_date       ), 'd-m-Y H:i') : NULL,
                        'car'               =>  [
                                                    'id'          => $serviceOrder->car->id,
                                                    'description' => $serviceOrder->car->brand . ' ' . $serviceOrder->car->model . ' ' . $serviceOrder->car->year
                                                ],
                        'owner'             =>  [
                                                    'id'   => $serviceOrder->carOwner->id,
                                                    'name' => $serviceOrder->carOwner->type == 'Business' ? 
                                                    $serviceOrder->carOwner->business_name :
                                                    $serviceOrder->carOwner->first_name . ' ' . $serviceOrder->carOwner->last_name . ' ' . $serviceOrder->carOwner->mother_maiden_name
                                                ],
                        'order_status'      =>  $serviceOrder->inventory->status,
                        'diagnostic_status' =>  $serviceOrder->diagnostic->status,
                        'quote_status'      =>  $serviceOrder->quote->status,
                    ];
                });
        } catch (Exception $e) {
            
        }
        return View::make('cars.record_list', [
            'version'  => 'record',
            'services' => $services, 
            'ini_date' => substr($ini_date,0,10), 
            'fin_date' => substr($fin_date,0,10)
            ]);         
    }


    public function exitWorkshop($service_order_id)
    {
        $result = ['success' => false];
        try {
            if (Auth::user() === NULL)
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
            if ($serviceOrder->workshop_id != Auth::user()->workshop_id)
            {
                $result['msj'] = 'No tienes permiso de ver esta orden de servicio';
                return Response::json($result);
            }
            if ($serviceOrder->exit_date)
            {
                $result['msj'] = 'salida ya registrada';
                return Response::json($result);
            }
            $serviceOrder->exit_date = Carbon::now('America/Mexico_City');
            $serviceOrder->car->service_order_id = NULL;
            Notification::
                  whereIn('type', array(
                        NotificationType::REQUEST_AGREE_SERVICEORDER,
                        NotificationType::REQUEST_AGREE_DIAGNOSTIC,
                        NotificationType::REQUEST_AGREE_QUOTE,
                        NotificationType::REQUEST_AGREE_DELIVERY) )
                ->where('data', $service_order_id)
                ->update(array('active' => 0));
            $serviceOrder->car->scheduledServices->map(function($scheduledService)use($serviceOrder) {
                if ($scheduledService->km <= $serviceOrder->inventory->km || 
                Carbon::now('America/Mexico_City')->endOfDay()->gt(new Carbon($scheduledService->date, 'America/Mexico_City'))) 
                    $scheduledService->service_order_id = $serviceOrder->id;
                else 
                    $scheduledService->notified = 0;
                $scheduledService->save();
            });
            $serviceOrder->save();
            $serviceOrder->car->save();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function carHistory($car_owner_id, $car_id) 
    {
        $result = ['success' => false];
        try {
            $carOwner = CarOwner::find($car_owner_id);
            if ($carOwner === NULL)
            {
                $result['msj'] = 'No existe el cliente';
                return Response::json($result);
            }
            $car = Car::find($car_id);
            if ($car === NULL)
            {
                $result['msj'] = 'Vehículo no encontrado';
                return $result;
            }
            if ($car->carOwner->id != $carOwner->id)
            {
                $result['msj'] = 'No tienes permiso de ver el historial de este vehículo';
                return Response::json($result);
            }
            $result['history'] = $car->serviceOrders
                ->sortByDesc('entry_date')
                ->map(function($serviceOrder) {
                    return [
                        'service_order_id'  => $serviceOrder->id,
                        'entry_date'        => date_format(date_create($serviceOrder->entry_date), 'd-m-Y H:i'),
                        'completion_date'   => $serviceOrder->completion_date ? date_format(date_create($serviceOrder->completion_date ), 'd-m-Y H:i') : NULL,
                        'exit_date'         => $serviceOrder->exit_date       ? date_format(date_create($serviceOrder->exit_date       ), 'd-m-Y H:i') : NULL,
                        'workshop'          => $serviceOrder->workshop->name,
                        'order_status'      => $serviceOrder->inventory->status,
                        'diagnostic_status' => $serviceOrder->diagnostic->status,
                        'quote_status'      => $serviceOrder->quote->status,
                    ];
                });
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function completedServiceByOwner($car_owner_id, $service_order_id) 
    {
        $result = ['success' => false];
        try {
            $carOwner = CarOwner::find($car_owner_id);
            if ($carOwner === NULL)
            {
                $result['msj'] = 'No existe el cliente';
                return Response::json($result);
            }
            $serviceOrder = ServiceOrder::find($service_order_id);
            if ($serviceOrder === NULL)
            {
                $result['msj'] = 'No existe la orden de servicio número ' . $service_order_id;
                return Response::json($result);
            }
            if ($serviceOrder->car_owner_id != $car_owner_id)
            {
                 $result['msj'] = 'No tienes permiso de ver esta orden de servicio';
                return Response::json($result);
            }
            if (!$serviceOrder->completion_date)
            {
                $result['msj'] = 'sin registro de servicio terminado';
                return Response::json($result);
            }
            $result['service_complete'] = [
                'date'          => date_format(date_create($serviceOrder->completion_date ), 'd-m-Y H:i'),
                'workshop_name' => $serviceOrder->workshop->name,
                'car'           => $serviceOrder->car->brand . ' ' . $serviceOrder->car->model . ' ' . $serviceOrder->car->year . ', N/S ' . $serviceOrder->car->serial_number,
            ]; 
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function knowCompleteService($car_owner_id, $service_order_id) 
    {
        $result = ['success' => false];
        try {
            $carOwner = CarOwner::find($car_owner_id);
            if ($carOwner === NULL)
            {
                $result['msj'] = 'No existe el cliente';
                return Response::json($result);
            }
            $serviceOrder = ServiceOrder::find($service_order_id);
            if ($serviceOrder === NULL)
            {
                $result['msj'] = 'No existe la orden de servicio número ' . $service_order_id;
                return Response::json($result);
            }
            if ($serviceOrder->car_owner_id != $car_owner_id)
            {
                 $result['msj'] = 'No tienes permiso de ver esta orden de servicio';
                return Response::json($result);
            }
            if (!$serviceOrder->completion_date)
            {
                $result['msj'] = 'sin registro de servicio terminado';
                return Response::json($result);
            }
            Notification::
                  where('type', NotificationType::REQUEST_AGREE_DELIVERY)
                ->where('data', $service_order_id)
                ->update(array('active' => 0));
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }
}

?>