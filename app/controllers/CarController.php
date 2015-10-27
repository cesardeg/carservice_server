<?php
use Carbon\Carbon;

class CarController extends Controller
{

    public function store($user_id)
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
            $owner = $input['car_owner_id'] ? 
                CarOwner::find($input['car_owner_id']) : CarOwner::where('username', $input['username'])->first();
            if ($owner === NULL)
            {
                $result['msj'] = 'no existe cliente, verificar nombre de usuario';
                return Response::json($result);
            }
            $car = new Car($input);
            $car->photo = base64_decode($input['photo']);
            $car->car_owner_id = $owner->id;
            $car->save();
            $result['id'] = $car->id;
            $result['success'] = true;
        } 
        catch (Exception $e)
        {
            if (contains($e->getMessage(), '1062') &&
                contains($e->getMessage(), 'key \'tag\'') )
            {
                $result['msj'] = 
                'Ya existe un vehículo con el mismo TAG, favor de verificar';
            } elseif (contains($e->getMessage(), '1062') &&
                     contains($e->getMessage(), 'key \'serial_number\'') )
            {
                $result['msj'] = 
                'Ya existe un vehículo con el mismo número de serie, favor de verificar';
            } elseif (contains($e->getMessage(), '1062') &&
                     contains($e->getMessage(), 'key \'license_plate\'') )
            {
                $result['msj'] = 
                'Ya existe un vehículo con el mismo número de placa, favor de verificar';
            }
            else
            {
                $result['msj'] = $e->getMessage();
            }
        }
        return Response::json($result);
    }
    
    public function findCarId($user_id, $key, $value)
    {
        $result = ['success' => false];
        try {
            $user = User::find($user_id);
            if ($user === NULL)
            {
                $result['msj'] = 'No ha iniciado sesión el usuario';
                return Response::json($result);
            }
            $car = Car::where(urldecode($key), urldecode($value))->first();
            if ($car === NULL)
            {
                $result['msj'] = 'Vehículo no encontrado';
                return Response::json($result);
            }
            $result['id'] = $car->id;
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function car($user_id, $car_id)
    {
        $result = ['success' => false];
        try{
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
            $result['car'] = [
                'owner'            => $car->carOwner->type == "Person"       ?
                                        $car->carOwner->first_name . ' ' . 
                                        $car->carOwner->last_name  . ' ' . 
                                        $car->carOwner->mother_maiden_name :
                                        $car->ownerName = $car->carOwner->business_name,
                'tag'              => $car->tag,
                'brand'            => $car->brand,
                'model'            => $car->model,
                'year'             => $car->year,
                'color'            => $car->color,
                'serial_number'    => $car->serial_number,
                'license_plate'    => $car->license_plate,
                'km'               => $car->km,
                'service_order_id' => $car->service_order_id,
                'photo'            => base64_encode($car->photo),
            ];
            $result['in_workshop'] = $car->service_order_id ? true : false;
            if ($result['in_workshop'])
            {
                $result['inventory_status' ] = $car->currentServiceOrder->inventory->status;
                $result['diagnostic_status'] = $car->currentServiceOrder->diagnostic->status;
                $result['quote_status'     ] = $car->currentServiceOrder->quote->status;
                $result['completed'        ] = $car->currentServiceOrder->completion_date ? true : false;
            }
            $result['success'] = true;
        
        } catch (Exception $e){
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);        
    }

    public function savePhoto($user_id, $car_id)
    {
        $result = ['success' => false];
        $input = Input::all();
        try{
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
            $car->photo = $input['photo'];
            $car->save();
            $result['success'] = true;
        } catch (Exception $e){
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function getByOwner($car_owner_id, $car_id)
    {
        $result = ['success' => false];
        try{
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
            if ($car->car_owner_id != $carOwner->id)
            {
                $result['msj'] = 'No tienes permiso de ver este vehículo';
                return Response::json($result);
            }
            $result['car'] = [
                'tag'           => $car->tag,
                'brand'         => $car->brand,
                'model'         => $car->model,
                'year'          => $car->year,
                'color'         => $car->color,
                'serial_number' => $car->serial_number,
                'license_plate' => $car->license_plate,
                'km'            => $car->km,
                'status'        => $car->service_order_id ? 'En taller' : 'En operacion',
                'photo'         => base64_encode($car->photo)
            ];
            $result['success'] = true;
        } catch (Exception $e){
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function getByAdmin($car_id)
    {
        $result = ['success' => false];
        try{
            if (Auth::user() === NULL)
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
            $result['car'] = [
                'brand'         => $car->brand,
                'model'         => $car->model,
                'year'          => $car->year,
                'color'         => $car->color,
                'serial_number' => $car->serial_number,
                'photo'         => base64_encode($car->photo)
            ];
            $result['success'] = true;
        
        } catch (Exception $e){
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);        
    }

    public function getReminderKmCapture($user_id, $car_id)
    {
        $result = ['success' => false];
        try {
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
            $result['reminder'] = Reminder::where('car_id' , $car_id)
                        ->where('subject', 'KmCapture')->first();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function remindKMCapture($user_id, $car_id)
    {
        $result = ['success' => false];
        $input = Input::all();
        try {
            $user = User::find($user_id);
            if ($user === NULL)
            {
                $result['msj'] = 'no ha iniciado sesión';
                return Response::json($result);
            }
            $car = Car::find($car_id);
            if ($car === NULL)
            {
                $result['msj'] = 'Vehículo no encontrado';
                return $result;
            }
            $reminder = Reminder::where('car_id' , $car_id)
                        ->where('subject', 'KmCapture')->first();
            $array = [
                'remind'            => $input['remind'],
                'subject'           => 'KmCapture',
                'frequency'         => 1,
                'time_unit'         => 'Semanas',
                'next_reminder'     => Carbon::now('America/Mexico_City')->addWeeks(1),
                'car_id'            => $car_id
            ];
            $reminder = $reminder === NULL ? new Reminder($array) : $reminder->fill($array);
            $reminder->save();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function updateReminderKmCapture($user_id, $car_id)
    {
        $result = ['success' => false];
        $input = Input::all();
        try {
            $user = User::find($user_id);
            if ($user === NULL)
            {
                $result['msj'] = 'no ha iniciado sesión';
                return Response::json($result);
            }
            $car = Car::find($car_id);
            if ($car === NULL)
            {
                $result['msj'] = 'Vehículo no encontrado';
                return $result;
            }
            $reminder = Reminder::where('car_id' , $car_id)
                        ->where('subject', 'KmCapture')->first();
            if ($reminder === NULL || !$reminder->remind)
            {
                $result['msj'] = 'recordatorios desactivados, no puede actualizar';
                return $result;
            }
            if (!$input['frequency']){
                $result['msj'] = 'frecuencia no puede ser 0';
                return $result;
            }
            $input['next_reminder'] = new Carbon($input['next_reminder']);
            $reminder->fill($input);
            $reminder->save();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function scheduledServices($user_id, $car_id)
    {
        $result = ['success' => false];
        try {
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
            $result['scheduled_services'] = $car->scheduledServices->filter(function($scheduledService) {
                return $scheduledService->service_order_id === NULL;
            })->map(function($scheduled_service){
                return [
                    'id'   => $scheduled_service->id,
                    'km'   => $scheduled_service->km,
                    'date' => date_format(date_create($scheduled_service->date), 'd-m-Y'),
                    'description' => $scheduled_service->description
                ];
            });
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function addScheduledService($user_id, $car_id)
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
            $car = Car::find($car_id);
            if ($car === NULL)
            {
                $result['msj'] = 'Vehículo no encontrado';
                return $result;
            }
            $scheduledService = new ScheduledService($input);
            $scheduledService->date = (new Carbon($input['date']))->startOfDay();
            $scheduledService->car_id = $car_id;
            $scheduledService->save();
            $result['id'] = $scheduledService->id;
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function removeScheduledService($user_id)
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
            $scheduledService = ScheduledService::find($input['scheduled_service_id']);
            $scheduledService->delete();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function kmCapture($car_owner_id, $car_id)
    {
        $result = ['success' => false];
        try{
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
                $result['msj'] = 'No tienes permiso de ver este vehículo';
                return Response::json($result);
            }
            $result['km_capture'] = [
                'car'           => $car->brand . ' ' . $car->model . ' ' . $car->year,
                'serial_number' => $car->serial_number,
                'last_km'       => $car->km
            ];
            $result['success'] = true;
        } catch (Exception $e){
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function updateKm($car_owner_id, $car_id)
    {
        $result = ['success' => false];
        try {
            $input = Input::all();
            $carOwner = CarOwner::find($car_owner_id);
            if ($carOwner === NULL)
            {
                $result['msj'] = 'No existe el cliente';
                return Response::json($result);
            }
            $car = Car::find($car_id);
            if ($car === NULL)
            {
                $result['msj'] = 'No existe el vehículo';
                return Response::json($result);
            }
            if ($car->carOwner->id != $carOwner->id)
            {
                $result['msj'] = 'No tienes permiso de actualizar el km de este vehículo';
                return Response::json($result);
            }
            if ($car->km > $input['km'])
            {
                $result['msj'] = 'Kilometraje menor al registrado anteriormente, favor de verificar ' . $input['km'];
                return Response::json($result);
            }
            $car->km = $input['km'];
            $car->save();
            Notification::where('car_owner_id', $car_owner_id)
                ->where('data', $car_id)
                ->where('active', 1)
                ->where('type', NotificationType::REMIND_KMCAPTURE)
                ->update(array('active' => 0));
            $scheduledServices = $car->scheduledServices->filter(
                function($scheduledService)use($car) {
                    return $car->km >= $scheduledService->km && 
                        $scheduledService->service_order_id === NULL && 
                        $scheduledService->notified === 0;
                })->sortByDesc('km');
            foreach ($scheduledServices as $scheduledService) {
                $data = [
                    'car_owner_id' => $car_owner_id,
                    'type'         => NotificationType::REMIND_SERVICETIME,
                    'title'        => 'Servicio programado', 
                    'message'      => 'Es tiempo de realizar servicio técnico a su vehículo',
                    'data'         => $scheduledService->id
                ];
                $notification = new Notification(array_merge($data, array('date' => Carbon::now('America/Mexico_City'))));
                $notification->save();
                Queue::push('SendNotification', $data);
                $scheduledService->notified = 1;
                $scheduledService->save();
            }
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    } 

    public function cars($car_owner_id)
    {
        $result = ['success' => false];
        try {
            $carOwner = CarOwner::find($car_owner_id);
            if ($carOwner === NULL)
            {
                $result['msj'] = 'No existe el cliente ' . $car_owner_id;
                return Response::json($result);
            }

            $cars = $carOwner->cars;

            $result['cars_in_operation'] = $cars->filter(function($car){
                return !$car->service_order_id;
            })->map(function($car) {
                return ['id' => $car->id, 
                    'description' => $car->brand . ' ' . $car->model . ' ' . $car->year,
                    'serial_number' => $car->serial_number];
            });

            $result['cars_in_workshop'] = $cars->filter(function($car) {
                return $car->service_order_id;
            })->map(function($car) {
                return ['id' => $car->id, 
                    'description' => $car->brand . ' ' . $car->model . ' ' . $car->year,
                    'serial_number' => $car->serial_number];
            });
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }
}


function contains($haystack, $needle)
{
    return strpos($haystack, $needle) !== false;
}

?>
