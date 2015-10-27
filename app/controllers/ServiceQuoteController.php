<?php
use Carbon\Carbon;

class ServiceQuoteController extends Controller
{
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
            if (!$serviceOrder->quote->date)
            {
                $serviceOrder->quote->date = Carbon::now('America/Mexico_City');
                $serviceOrder->quote->save();
            }
            $quote = $serviceOrder->quote;
            $result['quote'] = [
                'status'         => $quote->status,
                'date_created'   => $quote->date === NULL? 
                                       $quote->date : 
                                       date_format(date_create($quote->date          ), 'd-m-Y H:i'),
                'date_estimated' => $quote->estimated_date === NULL? 
                                       '' : 
                                       date_format(date_create($quote->estimated_date), 'd-m-Y H:i'),
                'date_closed'    => $quote->closed_date === NULL? 
                                       $quote->closed_date : 
                                       date_format(date_create($quote->closed_date   ), 'd-m-Y H:i'),
                'date_agree'     => $quote->agree_date === NULL? 
                                       $quote->agree_date : 
                                       date_format(date_create($quote->agree_date    ), 'd-m-Y H:i'),
                'date_disagree'  => $quote->disagree_date === NULL? 
                                       $quote->disagree_date : 
                                       date_format(date_create($quote->disagree_date ), 'd-m-Y H:i'),
                'quote_items'    => $quote->items->map(function($item){
                                        return [
                                            'id'          => $item->id,
                                            'description' => $item->description,
                                            'amount'      => $item->amount,
                                            'subtotal'    => $item->subtotal
                                        ];
                                    }),
                'subtotal'       => $quote->subtotal,
                'tax'            => $quote->tax,
                'total'          => $quote->total,
                'advance_payment'=> $quote->advance_payment,
                'editable'       => !$serviceOrder->exit_date,
            ];
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function saveQuoteItem($quote_item_id) {
        $result = ['success' => false];
        $item = NULL;
        try {
            $input = Input::all();
            if (Auth::user() === NULL)
            {
                $result['msj'] = 'Usuario no logeado';
                return Response::json($result);
            }
            if ($quote_item_id != 0){
                $item = ServiceQuoteItem::find($quote_item_id);
                $item->fill($input);
            } else {
                $item = new ServiceQuoteItem($input);
                $item->service_quote_id = ServiceOrder::find($input['service_order_id'])->quote->id;
            }
            if ($item->quote->serviceOrder->workshop_id != Auth::user()->workshop_id)
            {
                 $result['msj'] = 'No tienes permiso de guardar esté item';
                return Response::json($result);
            }
            if ($item->quote->serviceOrder->exit_date)
            {
                $result['msj'] = 'no se puede guardar, vehículo fuera de taller';
                return Response::json($result);
            }
            if ($item->quote->status != ServiceStatus::IN_PROCESS)
            {
                $result['msj'] = 'cotización cerrada, no se puede editar';
                return Response::json($result);
            }
            $item->save();
            $item->quote->updateTotal();
            $result['res'] = [
                'id'       => $item->id,
                'quote_subtotal' => $item->quote->subtotal,
                'quote_tax'      => $item->quote->tax,
                'quote_total'    => $item->quote->total
            ];
            $result['success']  = true;
        } catch (Exception $e) {
            $result['msj']  = $e->getMessage();
        }
        return Response::json($result);
    }

    public function deleteQuoteItem($quote_item_id) {
        $result = ['success' => false];
        try {
            if (Auth::user() === NULL)
            {
                $result['msj'] = 'Usuario no logeado';
                return Response::json($result);
            }
            $item = ServiceQuoteItem::find($quote_item_id);
            if ($item->quote->serviceOrder->workshop_id != Auth::user()->workshop_id)
            {
                 $result['msj'] = 'No tienes permiso de eliminar esté item';
                return Response::json($result);
            }
            if ($item->quote->serviceOrder->exit_date)
            {
                $result['msj'] = 'no se puede eliminar, vehículo fuera de taller';
                return Response::json($result);
            }
            if ($item->quote->status != ServiceStatus::IN_PROCESS)
            {
                $result['msj'] = 'cotización cerrada, no se puede editar';
                return Response::json($result);
            }
            $item->delete();
            $item->quote->updateTotal();
            $result['res'] = [
                'quote_subtotal' => $item->quote->subtotal,
                'quote_tax'      => $item->quote->tax,
                'quote_total'    => $item->quote->total
            ];
            $result['success']  = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function closeByAdmin($service_order_id)
    {
        $result = ['success' => false];
        try {
            $input = Input::all();
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
                $result['msj'] = 'no se puede cerrar, vehículo fuera de taller';
                return Response::json($result);
            }
            if ($serviceOrder->quote->status != ServiceStatus::IN_PROCESS)
            {
                $result['msj'] = 'cotización ya cerrada';
                return Response::json($result);
            }
            if (Carbon::now('America/Mexico_City')->today()->gt(new Carbon($input['estimated_date'], 'America/Mexico_City')))
            {
                $result['msj'] = 'fecha estimada no puede ser anterior a hoy';
                return Response::json($result);
            }
            if ($input['advance_payment'] > $serviceOrder->quote->total)
            {
                $result['msj'] = 'anticipo debe ser menor a total';
                return Response::json($result);
            }
            $serviceOrder->quote->estimated_date  = (new Carbon($input['estimated_date'], 'America/Mexico_City'))->endOfDay();
            $serviceOrder->quote->advance_payment = $input['advance_payment'];
            $serviceOrder->quote->closed_date     = Carbon::now('America/Mexico_City');
            $serviceOrder->quote->user_id         = Auth::user()->id;
            $serviceOrder->quote->status          = ServiceStatus::WAITING_AGREE;
            $serviceOrder->quote->save();
            $data = [
                'car_owner_id' => $serviceOrder->car_owner_id,
                'type'         => NotificationType::REQUEST_AGREE_QUOTE,
                'title'        => 'Cotización',
                'message'      => 'La cotización del servicio a su vehículo está lista',
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
            if ($serviceOrder->quote->status != ServiceStatus::WAITING_AGREE)
            {
                $result['msj'] = 'no se puede aceptar en este momento';
                return Response::json($result);
            }
            $serviceOrder->quote->agree_date = Carbon::now('America/Mexico_City');
            $serviceOrder->quote->status     = ServiceStatus::AGREED;
            Notification::
                  where('type', NotificationType::REQUEST_AGREE_QUOTE)
                ->where('data', $serviceOrder->quote->id)
                ->update(array('active' => 0));
            $serviceOrder->quote->save();
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
            if (!in_array($serviceOrder->quote->status, 
                array(ServiceStatus::WAITING_AGREE, ServiceStatus::DISAGREED)) )
            {
                $result['msj'] = 'no se puede rehacer';
                return Response::json($result);
            }
            $serviceOrder->quote->agree_date    = NULL;
            $serviceOrder->quote->disagree_date = NULL;
            $serviceOrder->quote->closed_date   = NULL;
            $serviceOrder->quote->status        = ServiceStatus::IN_PROCESS;
            Notification::
                  where('type', NotificationType::REQUEST_AGREE_QUOTE)
                ->where('data', $serviceOrder->quote->id)
                ->update(array('active' => 0));
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
            $quote = $serviceOrder->quote;
            $result['quote'] = [
                'status'         => $quote->status,
                'workshop_name'  => $serviceOrder->workshop->name,
                'user_in_charge' => $quote->user ? $quote->user->first_name . ' ' . $quote->user->last_name : 'Por definir',
                'created_date'   => $quote->date === NULL? 
                                       $quote->date : 
                                       date_format(date_create($quote->date          ), 'd-m-Y H:i'),
                'closed_date'    => $quote->closed_date === NULL? 
                                       $quote->closed_date : 
                                       date_format(date_create($quote->closed_date   ), 'd-m-Y H:i'),
                'estimated_date' => $quote->estimated_date === NULL? 
                                       $quote->estimated_date : 
                                       date_format(date_create($quote->estimated_date), 'd-m-Y H:i'),
                'agreed_date'    => $quote->agree_date === NULL? 
                                       $quote->agree_date : 
                                       date_format(date_create($quote->agree_date    ), 'd-m-Y H:i'),
                'disagreed_date' => $quote->disagree_date === NULL? 
                                       $quote->disagree_date : 
                                       date_format(date_create($quote->disagree_date ), 'd-m-Y H:i'),
                'car'            => $serviceOrder->car->brand . ' ' . $serviceOrder->car->model . ' ' . $serviceOrder->car->year . ', N/S ' . $serviceOrder->car->serial_number,
                'quote_items'    => $quote->items->map(function($item){
                                        return [
                                            'id'          => $item->id,
                                            'description' => $item->description,
                                            'amount'      => $item->amount,
                                            'subtotal'    => $item->subtotal
                                        ];
                                    }),
                'subtotal'       => $quote->subtotal,
                'tax'            => $quote->tax,
                'total'          => $quote->total,
                'advance_payment'=> $quote->advance_payment,
                'editable'       => !$serviceOrder->exit_date,
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
            if ($serviceOrder->quote->status != ServiceStatus::WAITING_AGREE)
            {
                $result['msj'] = 'no se puede aceptar en este momento';
                return Response::json($result);
            }
            $serviceOrder->quote->agree_date = Carbon::now('America/Mexico_City');
            $serviceOrder->quote->status     = ServiceStatus::AGREED;
            Notification::
                  where('type', NotificationType::REQUEST_AGREE_QUOTE)
                ->where('data', $service_order_id)
                ->update(array('active' => 0));
            $serviceOrder->quote->save();
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
            if ($serviceOrder->quote->status != ServiceStatus::WAITING_AGREE)
            {
                $result['msj'] = 'no se puede aceptar en este momento';
                return Response::json($result);
            }
            $serviceOrder->quote->disagree_date = Carbon::now('America/Mexico_City');
            $serviceOrder->quote->status        = ServiceStatus::DISAGREED;
            Notification::
                  where('type', NotificationType::REQUEST_AGREE_QUOTE)
                ->where('data', $service_order_id)
                ->update(array('active' => 0));
            $serviceOrder->quote->save();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

}

?>