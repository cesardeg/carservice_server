<?php


class ServiceOrderController extends Controller
{
	public function store()
	{
		$input = Input::all();
		$result = ['success' => 0];
		date_default_timezone_set('America/Mexico_City');
		try 
		{
			$serviceOrder = new ServiceOrder($input);
			$serviceOrder->date = date('Y-m-d H:i:s', time());
			$serviceOrder->save();
			$service_order_id = $serviceOrder->id;
			$serviceDiagnostic = new ServiceDiagnostic($input['diagnostic']);
			$serviceDiagnostic->service_order_id = $service_order_id;
			$serviceDiagnostic->save();

			foreach ($input['quoteItems'] as $item) {
				$quoteItem = new ServiceQuoteItem($item);
				$quoteItem->service_order_id = $service_order_id;
				$quoteItem->save();
			}
			$result['success'] = 1;
		}catch(Exception $e)
		{
			return Response::json(['msj' => $e->getMessage()]);
		}
		return Response::json($result);
	}
}

?>