<?php


class CarLineController extends Controller
{
	public function index($carbrand)
	{
        $result = CarLine::where('car_brand_id', $carbrand)->orderBy('name', 'asc')->get();
        return Response::json($result);
	}
    
}

?>