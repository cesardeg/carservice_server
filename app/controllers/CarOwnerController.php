<?php

class CarOwnerController extends Controller
{

    public function isAvailableUsername($username)
    {
        $result = ['success' => 0];
        $carowners = CarOwner::where('username', $username)->get();
        if (count($carowners) == 0)
            $result['success'] = 1;
        return Response::json($result);
    }

    public function store()
    {
        $input = Input::all();
        $result = ['success' => 0, 'msj' => ''];
        if (!isset($input['type']))
            return Response::json($result);

        if ($input['type'] == "Person" && 
            (!isset($input['first_name']) || !isset($input['last_name']) || !isset($input['mother_maiden_name']) ) )
            return Response::json($result);

        if ( $input['type'] == "Business" && 
            ( !isset($input['business_name']) || !isset($input['rfc']) ) )
            return Response::json($result);

        if (!isset($input['street']) || !isset($input['neighborhood']) || !isset($input['username']) ||
            !isset($input['state']) || !isset($input['town']) || !isset($input['client_id']) )
            return Response::json($result);

        $carowner = new CarOwner;
        $carowner->type = $input['type'];

        if ($input['type'] == "Person")
        {
            $carowner->first_name = $input['first_name'];
            $carowner->last_name = $input['last_name'];
            $carowner->mother_maiden_name = $input['mother_maiden_name'];
        }
        if ($input['type'] == "Business")
        {
            $carowner->business_name = $input['business_name'];
            $carowner->rfc = $input['rfc'];
        }
        $carowner->username = $input['username'];
        $carowner->street = $input['street'];
        $carowner->neighborhood = $input['neighborhood'];
        $carowner->state = $input['state'];
        $carowner->town = $input['town'];
        $carowner->client_id = $input['client_id'];

        if (isset($input['postal_code']))
            $carowner->postal_code = $input['postal_code'];

        if (isset($input['email']))
            $carowner->email = $input['email'];

        if (isset($input['mobile_phone_number']))
            $carowner->mobile_phone_number = $input['mobile_phone_number'];

        if (isset($input['phone_number']))
            $carowner->phone_number = $input['phone_number'];
        try
        {
            $carowner->save();
            $result['success'] = 1;
            $result['id'] = $carowner->id;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function index($client_id)
    {
        $owners = CarOwner::where('client_id', $client_id)->get();
        return Response::json($owners);
    }

    public function client()
    {
        return $this->belongsTo('Client');
    }
}

?>