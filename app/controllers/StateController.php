<?php


class StateController extends Controller
{
    public function index()
    {
        $result = State::orderBy('state', 'asc')->get();
        return Response::json($result);
    }
}

?>