<?php


class CarBrandController extends Controller
{
    public function index()
    {
        $result = CarBrand::select('id', 'name')->orderBy('name', 'asc')->get();
        return Response::json($result);
    }
}

?>