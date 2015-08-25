<?php

class CarOwnerController extends Controller
{

    public function store($user_id)
    {
        $result = ['success' => false];
        $user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
            return Response::json($result);
        }
        $input = Input::all();
        if (!isset($input['type']))
        {
            $result['msj'] = 'Favor de especficar el tipo de cliente';
            return Response::json($result);
        }

        if ($input['type'] == "Person" && 
            (!isset($input['first_name']) || !isset($input['last_name']) || !isset($input['mother_maiden_name']) ) )
        {
            $result['msj'] = 'Falta el nombre del cliente';
            return Response::json($result);
        }

        if ( $input['type'] == "Business" && 
            ( !isset($input['business_name']) || !isset($input['rfc']) ) )
        {
            $result['msj'] = 'Falta la razón social';
            return Response::json($result);
        }

        if (!isset($input['street']) || !isset($input['neighborhood']) || !isset($input['username']) ||
            !isset($input['state']) || !isset($input['town']))
        {
            $result['msj'] = 'Falta dirección';
            return Response::json($result);
        }

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
        $carowner->client_id = $user->workshop->client->id;

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
            $result['success'] = true;
            $result['car_owner_id'] = $carowner->id;
        } catch (Exception $e) {
            if (contains($e->getMessage(), '1062') &&
                contains($e->getMessage(), 'key \'username\'') )
            {
                $result['msj'] = 'El nombre de usuario ' . $input['username'] . ' no esta disponible';
            }
            else
                $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function index($user_id)
    {
        $result = ['success' => false];
        $user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
            return Response::json($result);
        }
        $owners = CarOwner::all()->map(function($owner)
            {
                $car_owner_data['id'] = $owner->id;
                $car_owner_data['type'] = $owner->type;
                if ($owner->type == 'Person')
                    $car_owner_data['name'] = $owner->first_name . ' ' . $owner->last_name . ' ' . $owner->mother_maiden_name;
                else
                    $car_owner_data['name'] = $owner->business_name;
                $car_owner_data['address']  = $owner->neighborhood . ', ' . $owner->town . ', ' . $owner->state;
                return $car_owner_data;
            });
        return Response::json($owners);
    }

    public function carOwner($user_id, $car_owner_id)
    {
        $result = ['success' => false];
        $user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No ha iniciado sesión el usuario';
            return Response::json($result);
        }
        $owner = CarOwner::find($car_owner_id);
        if ($owner === NULL)
        {
            $result['msj'] = 'No existe cliente';
            return Response::json($result);
        }
        unset($owner->username);
        unset($owner->password);
        unset($owner->created_at);
        unset($owner->updated_at);
        $result['car_owner'] = $owner;
        $result['success'] = true;
        return Response::json($result);
    }

    public function client()
    {
        return $this->belongsTo('Client');
    }
}

function contains($haystack, $needle)
{
    return strpos($haystack, $needle) !== false;
}

?>