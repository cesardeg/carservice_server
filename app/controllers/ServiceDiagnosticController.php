<?php
use Carbon\Carbon;

class ServiceDiagnosticController extends Controller
{
    public function getByOperator($user_id, $service_order_id)
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
            if ($serviceOrder->workshop->id != $user->workshop->id)
            {
                $result['msj'] = 'No tienes permiso de ver este inventario';
                return Response::json($result);
            }
            if ($serviceOrder->diagnostic->status != ServiceStatus::IN_PROCESS)
            {
                $result['msj'] = 'no se puede editar, diagnóstico cerrado';
                return Response::json($result);
            }
            if (!$serviceOrder->diagnostic->date)
            {
                $serviceOrder->diagnostic->date = Carbon::now('America/Mexico_City');
                $serviceOrder->diagnostic->save();
            }
            $result['service_diagnostic'] = [
                "tires"                => $serviceOrder->diagnostic->tires,
                "front_shock_absorber" => $serviceOrder->diagnostic->front_shock_absorber,
                "rear_shock_absorber"  => $serviceOrder->diagnostic->rear_shock_absorber,
                "front_brakes"         => $serviceOrder->diagnostic->front_brakes,
                "rear_brakes"          => $serviceOrder->diagnostic->rear_brakes,
                "suspension"           => $serviceOrder->diagnostic->suspension,
                "bands"                => $serviceOrder->diagnostic->bands,
                "description"          => $serviceOrder->diagnostic->description,
                "required_material"    => $serviceOrder->quote->items
            ];
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

	public function update($user_id, $service_order_id)
	{
		$result = ['success' => false];
        try {
            $input = Input::all();
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
                $result['msj'] = 'No tienes permiso de ver este inventario';
                return Response::json($result);
            }
            if ($serviceOrder->diagnostic->status != ServiceStatus::IN_PROCESS)
            {
                $result['msj'] = 'no se puede editar, diagnóstico cerrado';
                return Response::json($result);
            }
            if (!$serviceOrder->diagnostic->date)
            {
                $serviceOrder->diagnostic->date = new Carbon('America/Mexico_City');
                $serviceOrder->diagnostic->user_id = $user->id;
            }
            $serviceOrder->diagnostic->fill($input);
	        $serviceOrder->diagnostic->save();
            $ids = array();
            foreach ($input['required_material'] as $item)
            {
                if ($item['id'] == 0)
                {
                    $quoteItem = new ServiceQuoteItem();
                    $quoteItem->service_quote_id = $serviceOrder->quote->id;
                }
                else
                {
                    $quoteItem = ServiceQuoteItem::find($item['id']);
                }
                $quoteItem->fill($item);
                $quoteItem->save();
                $ids[] = $quoteItem->id;
            }
            $serviceOrder->quote->items->map(function($item)use($ids) {
                if (!in_array($item->id, $ids))
                    $item->delete();
            });
	        $result['success'] = true;
        }
        catch (Exception $e) {
        	$result['msj'] = $e->getMessage();
        }
        return Response::json($result);
	}

    public function close($user_id, $service_order_id)
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
            if ($serviceOrder->workshop->id != $user->workshop->id)
            {
                $result['msj'] = 'No tienes permiso de ver este diagnóstico';
                return Response::json($result);
            }
            if ($serviceOrder->diagnostic->status != ServiceStatus::IN_PROCESS)
            {
                $result['msj'] = 'diagnóstico ya cerrado';
                return Response::json($result);
            }
            $serviceOrder->diagnostic->closed_date = Carbon::now('America/Mexico_City');
            $serviceOrder->diagnostic->user_id     = $user_id;
            $serviceOrder->diagnostic->status      = ServiceStatus::WAITING_AGREE;
            $serviceOrder->quote->status           = ServiceStatus::IN_PROCESS;
            $data = [
                'car_owner_id' => $serviceOrder->car_owner_id,
                'type'         => NotificationType::REQUEST_AGREE_DIAGNOSTIC, 
                'title'        => 'Diagnóstico',
                'message'      => 'El diagnóstico de su vehículo está listo',
                'data'         => $service_order_id
            ];
            $notification = new Notification(array_merge($data, array('date' => Carbon::now('America/Mexico_City'))));
            $serviceOrder->diagnostic->save();
            $serviceOrder->quote->save();
            $notification->save();
            Queue::push('SendNotification', $data);
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        
        return Response::json($result);
    }

    public function getByAdmin($service_order_id)
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
            if ($serviceOrder->user->workshop_id != Auth::user()->workshop_id)
            {
                 $result['msj'] = 'No tienes permiso de ver esta orden de servicio';
                return Response::json($result);
            }
            $diagnostic = $serviceOrder->diagnostic;
            $result['service_diagnostic'] = [
                'status'               => $diagnostic->status,
                'user_in_charge'       => $diagnostic->user ? $diagnostic->user->first_name . ' ' . $diagnostic->user->last_name : '',
                'date_created'         => $diagnostic->date === NULL? 
                                            $diagnostic->date : 
                                            date_format(date_create($diagnostic->date), 'd-m-Y H:i'),
                'date_closed'          => $diagnostic->closed_date === NULL? 
                                            $diagnostic->closed_date : 
                                            date_format(date_create($diagnostic->closed_date), 'd-m-Y H:i'),
                'date_agree'           => $diagnostic->agree_date === NULL? 
                                            $diagnostic->agree_date : 
                                            date_format(date_create($diagnostic->agree_date), 'd-m-Y H:i'),
                'date_disagree'        => $diagnostic->disagree_date === NULL? 
                                            $diagnostic->disagree_date : 
                                            date_format(date_create($diagnostic->disagree_date), 'd-m-Y H:i'),
                'tires'                => $diagnostic->tires,
                'front_shock_absorber' => $diagnostic->front_shock_absorber,
                'rear_shock_absorber'  => $diagnostic->rear_shock_absorber,
                'front_brakes'         => $diagnostic->front_brakes,
                'rear_brakes'          => $diagnostic->rear_brakes,
                'suspension'           => $diagnostic->suspension,
                'bands'                => $diagnostic->bands,
                'description'          => $diagnostic->description,
                'editable'             => !$serviceOrder->exit_date,
            ];
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function agreeByAdmin($service_order_id)
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
            if ($serviceOrder->user->workshop_id != Auth::user()->workshop_id)
            {
                 $result['msj'] = 'No tienes permiso de ver esta orden de servicio';
                return Response::json($result);
            }
            if ($serviceOrder->exit_date)
            {
                $result['msj'] = 'no se puede aceptar, vehículo fuera de taller';
                return Response::json($result);
            }
            if ($serviceOrder->diagnostic->status != ServiceStatus::WAITING_AGREE)
            {
                $result['msj'] = 'no se puede aceptar en este momento';
                return Response::json($result);
            }
            $serviceOrder->diagnostic->agree_date = Carbon::now('America/Mexico_City');
            $serviceOrder->diagnostic->status     = ServiceStatus::AGREED;
            Notification::
                  where('type', NotificationType::REQUEST_AGREE_DIAGNOSTIC)
                ->where('data', $service_order_id)
                ->update(array('active' => 0));
            $serviceOrder->diagnostic->save();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function redoByAdmin($service_order_id)
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
            if ($serviceOrder->user->workshop_id != Auth::user()->workshop_id)
            {
                 $result['msj'] = 'No tienes permiso de ver esta orden de servicio';
                return Response::json($result);
            }
            if ($serviceOrder->exit_date)
            {
                $result['msj'] = 'no se puede rehacer, vehículo fuera de taller';
                return Response::json($result);
            }
            if (!in_array($serviceOrder->diagnostic->status, 
                array(ServiceStatus::WAITING_AGREE, ServiceStatus::DISAGREED)) )
            {
                $result['msj'] = 'no se puede rehacer';
                return Response::json($result);
            }
            $serviceOrder->diagnostic->agree_date    = NULL;
            $serviceOrder->diagnostic->disagree_date = NULL;
            $serviceOrder->diagnostic->closed_date   = NULL;
            $serviceOrder->diagnostic->status        = ServiceStatus::IN_PROCESS;
            $serviceOrder->quote->agree_date    = NULL;
            $serviceOrder->quote->disagree_date = NULL;
            $serviceOrder->quote->closed_date   = NULL;
            $serviceOrder->quote->status        = ServiceStatus::NO_AVAILABLE;
            Notification::
                  where('type', NotificationType::REQUEST_AGREE_DIAGNOSTIC)
                ->where('data', $service_order_id)
                ->update(array('active' => 0));
            $serviceOrder->diagnostic->save();
            $serviceOrder->quote->save();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function getByOwner($car_owner_id, $service_order_id)
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
            $diagnostic = $serviceOrder->diagnostic;
            $result['service_diagnostic'] = [
                'status'               => $diagnostic->status,
                'user_in_charge'       => $diagnostic->user ? $diagnostic->user->first_name . ' ' . $diagnostic->user->last_name : 'Por definir',
                'workshop_name'        => $serviceOrder->workshop->name,
                'car'                  => $serviceOrder->car->brand . ' ' . $serviceOrder->car->model . ' ' . $serviceOrder->car->year . ', N/S ' . $serviceOrder->car->serial_number,
                'created_date'         => $diagnostic->date === NULL? 
                                            $diagnostic->date : 
                                            date_format(date_create($diagnostic->date), 'd-m-Y H:i'),
                'closed_date'          => $diagnostic->closed_date === NULL? 
                                            $diagnostic->closed_date : 
                                            date_format(date_create($diagnostic->closed_date), 'd-m-Y H:i'),
                'agreed_date'          => $diagnostic->agree_date === NULL? 
                                            $diagnostic->agree_date : 
                                            date_format(date_create($diagnostic->agree_date), 'd-m-Y H:i'),
                'disagreed_date'       => $diagnostic->disagree_date === NULL? 
                                            $diagnostic->disagree_date : 
                                            date_format(date_create($diagnostic->disagree_date), 'd-m-Y H:i'),
                'tires'                => $diagnostic->tires,
                'front_shock_absorber' => $diagnostic->front_shock_absorber,
                'rear_shock_absorber'  => $diagnostic->rear_shock_absorber,
                'front_brakes'         => $diagnostic->front_brakes,
                'rear_brakes'          => $diagnostic->rear_brakes,
                'suspension'           => $diagnostic->suspension,
                'bands'                => $diagnostic->bands,
                'description'          => $diagnostic->description,
                'editable'             => !$serviceOrder->exit_date,
            ];
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function agreeByOwner($car_owner_id, $service_order_id)
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
                 $result['msj'] = 'No tienes permiso para autorizar esta orden de servicio';
                return Response::json($result);
            }
            if ($serviceOrder->exit_date)
            {
                $result['msj'] = 'no se puede aceptar, vehículo fuera de taller';
                return Response::json($result);
            }
            if ($serviceOrder->diagnostic->status != ServiceStatus::WAITING_AGREE)
            {
                $result['msj'] = 'no se puede aceptar en este momento';
                return Response::json($result);
            }
            $serviceOrder->diagnostic->agree_date = Carbon::now('America/Mexico_City');
            $serviceOrder->diagnostic->status     = ServiceStatus::AGREED;
            Notification::
                  where('type', NotificationType::REQUEST_AGREE_DIAGNOSTIC)
                ->where('data', $service_order_id)
                ->update(array('active' => 0));
            $serviceOrder->diagnostic->save();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function disagreeByOwner($car_owner_id, $service_order_id)
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
            if ($serviceOrder->car_owner_id != $carOwner->id)
            {
                 $result['msj'] = 'No tienes permiso para autorizar esta orden de servicio';
                return Response::json($result);
            }
            if ($serviceOrder->exit_date)
            {
                $result['msj'] = 'no se puede aceptar, vehículo fuera de taller';
                return Response::json($result);
            }
            if ($serviceOrder->diagnostic->status != ServiceStatus::WAITING_AGREE)
            {
                $result['msj'] = 'no se puede aceptar en este momento';
                return Response::json($result);
            }
            $serviceOrder->diagnostic->disagree_date = Carbon::now('America/Mexico_City');
            $serviceOrder->diagnostic->status        = ServiceStatus::DISAGREED;
            Notification::
                  where('type', NotificationType::REQUEST_AGREE_DIAGNOSTIC)
                ->where('data', $service_order_id)
                ->update(array('active' => 0));
            $serviceOrder->diagnostic->save();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }
}

?>