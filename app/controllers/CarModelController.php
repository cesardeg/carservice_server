<?php


class CarModelController extends Controller
{
	public function index($carbrand)
	{
        $result = CarModel::select(array('id', 'name'))->where('car_brand_id', $carbrand)->orderBy('name', 'asc')->get();
        return Response::json($result);
	}
    
}

?>