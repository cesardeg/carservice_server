<?php


class TownController extends Controller
{
    public function index($state_id)
    {
        $result = Town::where('state_id', $state_id)->orderBy('town', 'asc')->get();
        return Response::json($result);
    }
}

?>