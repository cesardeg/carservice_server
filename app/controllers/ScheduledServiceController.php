<?php
class ScheduledServiceController extends Controller
{
    public function scheduledService($car_owner_id, $scheduled_service_id)
    {
        $result = ['success' => false];
        try {
            $carOwner = CarOwner::find($car_owner_id);
            if ($carOwner === NULL)
            {
                $result['msj'] = 'No existe el cliente ' . $car_owner_id;
                return Response::json($result);
            }
            $scheduledService = ScheduledService::find($scheduled_service_id);
            if ($scheduledService === NULL)
            {
                $result['msj'] = 'No existe el servicio programado ' . $scheduled_service_id;
                return Response::json($result);
            }
            if ($scheduledService->car->carOwner->id != $car_owner_id)
            {
                $result['msj'] = 'No tienes permiso de ver este servicio ';
                return Response::json($result);
            }
            $result['scheduled_service'] = [
                'car' => $scheduledService->car->brand . ' ' . $scheduledService->car->model . ' ' . $scheduledService->car->year,
                'serial_number' => $scheduledService->car->serial_number,
                'current_km'    => $scheduledService->car->km,
                'required_km'   => $scheduledService->km,
                'required_date' => date_format(date_create($scheduledService->date), 'd-m-Y'),
                'description'   => $scheduledService->description
            ];
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }
}
?>