<?php
use Carbon\Carbon;

class ServicePhotoController extends Controller
{

	public function listPhotos($user_id, $service_order_id, $type)
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
			if ($serviceOrder->workshop_id != $user->workshop_id)
			{
				$result['msj'] = 'No tienes permiso de ver las fotos de esta orden de servicio';
				return Response::json($result);
			}
			$result['photos'] = ServicePhoto::
			  where('service_inventory_id', $serviceOrder->inventory->id)
			->where('type', $type)
			->get()
			->map(function($servicePhoto){
			return [
				'id'    => $servicePhoto->id,
				'photo' => base64_encode($servicePhoto->photo)
			];
		});
			$result['success'] = true;
		} catch (Exception $e) {
			$result['msj'] = $e->getMessage();
		}
		return Response::json($result);
	}

	public function listPhotosOwner($car_owner_id, $service_order_id, $type)
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
			$result['photos'] = ServicePhoto::
				  where('service_inventory_id', $serviceOrder->inventory->id)
				->where('type', $type)->get()
				->map(function($servicePhoto) {
					return base64_encode($servicePhoto->photo);
				});
			$result['success'] = true;
		} catch (Exception $e) {
			$result['msj'] = $e->getMessage();
		}
		return Response::json($result);
	}

	public function listPhotosAdmin($service_order_id, $type)
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
				$result['msj'] = 'No tienes permiso de ver las fotos de esta orden de servicio';
				return Response::json($result);
			}
			$result['photos'] = ServicePhoto::
				  where('service_inventory_id', $serviceOrder->inventory->id)
				->where('type', $type)
				->get()
				->map(function($servicePhoto) {
					return base64_encode($servicePhoto->photo);
				});
			$result['success'] = true;
		} catch (Exception $e) {
			$result['msj'] = $e->getMessage();
		}
		return Response::json($result);
	}

	public function add($user_id, $service_order_id, $type) {
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
			if ($serviceOrder->workshop_id != $user->workshop_id)
			{
				 $result['msj'] = 'sin permiso para agregar fotos';
				return Response::json($result);
			}
			if ($serviceOrder->inventory->status !== ServiceStatus::IN_PROCESS)
			{
				$result['msj'] = 'orden de servicio cerrada, no se puede editar';
				return Response::json($result);
			}
			$photo = new ServicePhoto([
				'service_inventory_id' => $serviceOrder->inventory->id, 
				'type'                 => $type, 
				'photo'                => base64_decode($input['photo']),
			]);
			$photo->save();
			$result['photo_id'] = $photo->id;
			$result['success'] = true;
		} catch (Exception $e) {
			$result['msj'] = $e->getMessage();
		}
		return Response::json($result);
	}

	public function remove($user_id, $photo_id) {
		$result = ['success' => false];
		$result = ['success' => false];
		try {
			$input = Input::all();
			$user = User::find($user_id);
			if ($user === NULL)
			{
				$result['msj'] = 'No ha iniciado sesión el usuario';
				return Response::json($result);
			}
			$photo = ServicePhoto::find($photo_id);
			if ($photo === NULL)
			{
				$result['msj'] = 'No existe la foto';
				return Response::json($result);
			}
			if ($photo->inventory->serviceOrder->workshop_id != $user->workshop_id)
			{
				 $result['msj'] = 'No tienes permiso de eliminar esta foto';
				return Response::json($result);
			}
			if ($photo->inventory->serviceOrder->status !== ServiceStatus::IN_PROCESS)
			{
				$result['msj'] = 'Esta orden de servicio ya se encuentra cerrada, no se puede eliminar la foto';
				return Response::json($result);
			}
			$photo->delete();
			$result['success'] = true;
		} catch (Exception $e) {
			$result['msj'] = $e->getMessage();
		}
		return Response::json($result);
		return Response::json($result);
	}


	

}

?>