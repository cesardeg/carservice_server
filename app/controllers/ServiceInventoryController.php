<?php
use Carbon\Carbon;
use Sly\NotificationPusher\PushManager,
	Sly\NotificationPusher\Adapter\Gcm as GcmAdapter,
	Sly\NotificationPusher\Collection\DeviceCollection,
	Sly\NotificationPusher\Model\Device,
	Sly\NotificationPusher\Model\Message,
	Sly\NotificationPusher\Model\Push;

class ServiceInventoryController extends Controller
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
			if ($serviceOrder->inventory->status != ServiceStatus::IN_PROCESS)
			{
				$result['msj'] = 'no se puede editar, invemtario cerrado';
				return Response::json($result);
			}
			if (!$serviceOrder->inventory->date)
			{
				$serviceOrder->inventory->date = Carbon::now('America/Mexico_City');
				$serviceOrder->inventory->save();
			}
			$result['service_inventory'] = [
				"km"                   => $serviceOrder->inventory->km,
				"fuel_level"           => $serviceOrder->inventory->fuel_level,
				"brake_fluid"          => $serviceOrder->inventory->brake_fluid,
				"wiper_fluid"          => $serviceOrder->inventory->wiper_fluid,
				"antifreeze"           => $serviceOrder->inventory->antifreeze,
				"oil"                  => $serviceOrder->inventory->oil,
				"power_steering_fluid" => $serviceOrder->inventory->power_steering_fluid,
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
		try 
		{
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
				 $result['msj'] = 'No tienes permiso de ver esta orden de servicio';
				return Response::json($result);
			}
			if ($serviceOrder->inventory->status != ServiceStatus::IN_PROCESS)
			{
				$result['msj'] = 'inventario cerrado, no se puede actualizar';
				return Response::json($result);
			}
			$serviceOrder->inventory->fill($input);
			$serviceOrder->inventory->save();
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
		try{
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
			if ($serviceOrder->inventory->status != ServiceStatus::IN_PROCESS)
			{
				$result['msj'] = 'inventario ya se encuentra cerrado';
				return Response::json($result);
			}
			if ($serviceOrder->car->km > $serviceOrder->inventory->km)
			{
				$result['msj'] = 'Kilometraje menor al registrado anteriormente, favor de verificar';
				return Response::json($result);
			}
			$serviceOrder->inventory->closed_date = Carbon::now('America/Mexico_City');
			$serviceOrder->inventory->user_id     = $user_id;
			$serviceOrder->inventory->status      = ServiceStatus::WAITING_AGREE;
			if ($serviceOrder->diagnostic->status == ServiceStatus::NO_AVAILABLE)
				$serviceOrder->diagnostic->status = ServiceStatus::IN_PROCESS;
			$serviceOrder->car->km = $serviceOrder->inventory->km;
			$data = [
				'car_owner_id' => $serviceOrder->car_owner_id,
				'type'         => NotificationType::REQUEST_AGREE_SERVICEORDER,
				'title'        => 'Entrada vehículo a taller',
				'message'      => 'Se ha registrado la entrada de su vehículo a taller',
				'data'         => $service_order_id
			];
			$notification = new Notification(array_merge($data, array('date' => Carbon::now('America/Mexico_City'))));
			$serviceOrder->inventory->save();
			$serviceOrder->diagnostic->save();
			$serviceOrder->car->save();
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
			$inventory = $serviceOrder->inventory;
			$result['service_inventory'] = [
				'service_name'           => $serviceOrder->service_name,
				'owner_supplied_parts'   => $serviceOrder->owner_supplied_parts   ? 'Si': 'No',
				'owner_allow_used_parts' => $serviceOrder->owner_allow_used_parts ? 'Si': 'No',
				'pick_up_address'        => $serviceOrder->pick_up_address,
				'delivery_address'       => $serviceOrder->delivery_address,
				'status'                 => $inventory->status,
				'user_in_charge'         => $inventory->user? $inventory->user->first_name . ' ' . $inventory->user->last_name : '',
				'creation_date'          => $inventory->date          === NULL ?
												$inventory->date           : 
												date_format(date_create($inventory->date         ), 'd-m-Y H:i'),
				'closed_date'            => $inventory->closed_date   === NULL ?
												$inventory->closed_date      : 
												date_format(date_create($inventory->closed_date  ), 'd-m-Y H:i'),
				'agree_date'             => $inventory->agree_date    === NULL ?
												$inventory->agree_date    : 
												date_format(date_create($inventory->agree_date   ), 'd-m-Y H:i'),
				'disagree_date'          => $inventory->disagree_date === NULL ?
												$inventory->disagree_date : 
												date_format(date_create($inventory->disagree_date), 'd-m-Y H:i'),
				'km'                     => $inventory->km,
				'fuel_level'             => $inventory->fuel_level,
				'brake_fluid'            => $inventory->brake_fluid,
				'wiper_fluid'            => $inventory->wiper_fluid,
				'antifreeze'             => $inventory->antifreeze,
				'oil'                    => $inventory->oil,
				'power_steering_fluid'   => $inventory->power_steering_fluid,
				'editable'               => !$serviceOrder->exit_date,
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
			if ($serviceOrder->inventory->status != ServiceStatus::WAITING_AGREE)
			{
				$result['msj'] = 'no se puede aceptar en este momento';
				return Response::json($result);
			}
			$serviceOrder->inventory->agree_date = Carbon::now('America/Mexico_City');
			$serviceOrder->inventory->status     = ServiceStatus::AGREED;
			Notification::
				  where('type', NotificationType::REQUEST_AGREE_SERVICEORDER)
				->where('data', $service_order_id)
				->update(array('active' => 0));
			$serviceOrder->inventory->save();
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
			if (!in_array($serviceOrder->inventory->status, 
				array(ServiceStatus::WAITING_AGREE, ServiceStatus::DISAGREED)) )
			{
				$result['msj'] = 'no se puede rehacer';
				return Response::json($result);
			}
			$serviceOrder->inventory->agree_date    = NULL;
			$serviceOrder->inventory->disagree_date = NULL;
			$serviceOrder->inventory->closed_date   = NULL;
			$serviceOrder->inventory->status        = ServiceStatus::IN_PROCESS;
			Notification::
				  where('type', NotificationType::REQUEST_AGREE_SERVICEORDER)
				->where('data', $service_order_id)
				->update(array('active' => 0));
			$serviceOrder->inventory->save();
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
			$inventory = $serviceOrder->inventory;
			$result['service_inventory'] = [
				'status'                 => $inventory->status,
				'user_in_charge'         => $inventory->user? $inventory->user->first_name . ' ' . $inventory->user->last_name : 'Por definir',
				'workshop_name'          => $serviceOrder->workshop->name,
				'car'                    => $serviceOrder->car->brand . ' ' . $serviceOrder->car->model . ' ' . $serviceOrder->car->year . ', N/S ' . $serviceOrder->car->serial_number,
				'service_name'           => $serviceOrder->service_name,
				'owner_supplied_parts'   => $serviceOrder->owner_supplied_parts   ? 'Si': 'No',
				'owner_allow_used_parts' => $serviceOrder->owner_allow_used_parts ? 'Si': 'No',
				'pick_up_address'        => $serviceOrder->pick_up_address,
				'delivery_address'       => $serviceOrder->delivery_address,
				'created_date'           => $inventory->date          === NULL ?
												$inventory->date           : 
												date_format(date_create($inventory->date         ), 'd-m-Y H:i'),
				'closed_date'            => $inventory->closed_date   === NULL ?
												$inventory->closed_date      : 
												date_format(date_create($inventory->closed_date  ), 'd-m-Y H:i'),
				'agreed_date'            => $inventory->agree_date    === NULL ?
												$inventory->agree_date    : 
												date_format(date_create($inventory->agree_date   ), 'd-m-Y H:i'),
				'disagreed_date'         => $inventory->disagree_date === NULL ?
												$inventory->disagree_date : 
												date_format(date_create($inventory->disagree_date), 'd-m-Y H:i'),
				'km'                     => $inventory->km,
				'fuel_level'             => $inventory->fuel_level,
				'brake_fluid'            => $inventory->brake_fluid,
				'wiper_fluid'            => $inventory->wiper_fluid,
				'antifreeze'             => $inventory->antifreeze,
				'oil'                    => $inventory->oil,
				'power_steering_fluid'   => $inventory->power_steering_fluid,
				'editable'               => !$serviceOrder->exit_date,
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
			if ($serviceOrder->inventory->status != ServiceStatus::WAITING_AGREE)
			{
				$result['msj'] = 'no se puede aceptar en este momento';
				return Response::json($result);
			}
			$serviceOrder->inventory->agree_date = Carbon::now('America/Mexico_City');
			$serviceOrder->inventory->status     = ServiceStatus::AGREED;
			Notification::
				  where('type', NotificationType::REQUEST_AGREE_SERVICEORDER)
				->where('data', $service_order_id)
				->update(array('active' => 0));
			$serviceOrder->inventory->save();
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
			if ($serviceOrder->inventory->status != ServiceStatus::WAITING_AGREE)
			{
				$result['msj'] = 'no se puede aceptar en este momento';
				return Response::json($result);
			}
			$serviceOrder->inventory->disagree_date = Carbon::now('America/Mexico_City');
			$serviceOrder->inventory->status        = ServiceStatus::DISAGREED;
			Notification::
				  where('type', NotificationType::REQUEST_AGREE_SERVICEORDER)
				->where('data', $service_order_id)
				->update(array('active' => 0));
			$serviceOrder->inventory->save();
			$result['success'] = true;
		} catch (Exception $e) {
			$result['msj'] = $e->getMessage();
		}
		return Response::json($result);
	}
}

?>