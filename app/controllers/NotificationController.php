<?php
class NotificationController extends Controller
{
    public function notifications($car_owner_id)
    {
        $result = ['success' => false];
        try {
            $carOwner = CarOwner::find($car_owner_id);
            if ($carOwner === NULL)
            {
                $result['msj'] = 'No existe el cliente ' . $car_owner_id;
                return Response::json($result);
            }
            $result['notifications'] = $carOwner->notifications->filter(function($notification){
                return $notification->active == 1;
            })->sortByDesc('date')->map(function($notification){
                return [
                    'title'   => $notification->title,
                    'date'    => date_format(date_create($notification->date), 'd-m-Y H:i'), 
                    'type'    => $notification->type,
                    'message' => $notification->message,
                    'data'    => $notification->data
                ];
            });
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }
}
?>