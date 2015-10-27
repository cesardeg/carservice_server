<?php

class CarOwnerController extends Controller
{
    public function login()
    {
        $input = Input::all(); 
        $result = ['success' => false];
        try
        {
            $carowner = CarOwner::where('username', $input['username'])
                ->where(DB::raw('BINARY `password`'), $input['password'])->first();
            if ($carowner === NULL)
            {
                $result['msj'] = "nombre de usuario y/o contraseña incorrectos";
                return Response::json($result);
            }
            $result['id'] = $carowner->id;
            $result['success'] = true;
        }
        catch(Exception $e)
        {
            $result['msj'] = $e->getMessage();
        } 
        return Response::json($result);
    }

    public function registerToken($car_owner_id) {
        $result = ['success' => false];
        $input = Input::all();
        try {
            $carowner = CarOwner::find($car_owner_id);
            if ($carowner === NULL)
            {
                $result['msj'] = "No existe cliente";
                return Response::json($result);
            }
            Token::where('token', $input['token'])->update(array('active' => 0));
            $token = Token::where('car_owner_id', $car_owner_id)->where('token', $input['token'])->first();
            if ($token === NULL) {
                $token = new Token($input);
                $token->car_owner_id = $car_owner_id; 
            }
            $token->active = true;
            $token->save();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function unregisterToken($car_owner_id) {
        $result = ['success' => false];
        $input = Input::all();
        try {
            $carowner = CarOwner::find($car_owner_id);
            if ($carowner === NULL)
            {
                $result['msj'] = "No existe cliente";
                return Response::json($result);
            }
            $token = Token::
                  where('car_owner_id', $car_owner_id)
                ->where('token', $input['token'])
                ->update(array('active' => 0));
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function ownerName($car_owner_id)
    {
        $result = ['success' => false];
        try
        {
            $carowner = CarOwner::find($car_owner_id);
            if ($carowner === NULL)
            {
                $result['msj'] = "No existe usuario";
                return Response::json($result);
            }
            $result['owner_name'] = [
                'name'     => $carowner->type == 'Person' ? 
                                $carowner->first_name . ' ' . $carowner->last_name . ' ' . $carowner->mother_maiden_name : 
                                $carowner->business_name,
                'username' => $carowner->username
            ];
            $result['success'] = true;
        }
        catch(Exception $e)
        {
            $result['msj'] = $e->getMessage();
        } 
        return Response::json($result);

    }

    public function store($user_id)
    {
        $result = ['success' => false];     
        try
        {
            $user = User::find($user_id);
            if ($user === NULL)
            {
                $result['msj'] = 'No ha iniciado sesión el usuario';
                return Response::json($result);
            }
            $input = Input::all();
            $carowner = new CarOwner($input);
            $carowner->username = $input['username'];
            $carowner->password = $input['password'];
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

    public function save($car_owner_id)
    {
        $result = ['success' => false];

        try {
            $input = Input::all();
            if ($car_owner_id != 0)
            {
                $carowner = CarOwner::find($car_owner_id);
                if ($carowner === NULL)
                {
                    $result['msj'] = 'No existe usuario';
                    return Response::json($result);
                }
                $carowner->fill($input);
            }
            else
            {
                $carowner = new CarOwner($input);
            }   
            $carowner->username = $input['username'];
            $carowner->password = $input['password'];
            $carowner->save();
            $result['success'] = true;
        } catch (Exception $e) {
            if (contains($e->getMessage(), '1062') &&
                contains($e->getMessage(), 'key \'username\'') )
            {
                $result['msj'] = 'El nombre de usuario ' . $input['username'] . ' no esta disponible';
            }
            else 
            {
                $result['msj'] = $e->getMessage();
            }
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
        $owners = CarOwner::where('client_id', $user->workshop->client_id)->get()->map(function($owner) {
                $car_owner_data['id'] = $owner->id;
                $car_owner_data['type'] = $owner->type;
                if ($owner->type == 'Person')
                    $car_owner_data['name'] = $owner->first_name . ' ' . $owner->last_name . ' ' . $owner->mother_maiden_name;
                else
                    $car_owner_data['name'] = $owner->business_name;
                $car_owner_data['address']  = $owner->username;
                return $car_owner_data;
            });
        return Response::json($owners);
    }

    public function carOwner($user_id, $car_owner_id)
    {
        $result = ['success' => false];
        try {
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
            $result['car_owner'] = [
                'type'               => $owner->type,
                'business_name'      => $owner->business_name,
                'rfc'                => $owner->rfc,
                'first_name'         => $owner->first_name,
                'last_name'          => $owner->last_name,
                'mother_maiden_name' => $owner->mother_maiden_name,
                'street'             => $owner->street,
                'neighborhood'       => $owner->neighborhood,
                'state'              => $owner->state ? $owner->state->state : '',
                'town'               => $owner->town  ? $owner->town->town   : '',
                'postal_code'        => $owner->postal_code,
                'phone_number'       => $owner->phone_number,
                'mobile_phone_number'=> $owner->mobile_phone_number,
                'email'              => $owner->email,
                'username'           => $owner->username
            ];
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function getByAdmin($car_owner_id)
    {
        $result = ['success' => false];
        try {
            if (Auth::user() === NULL)
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
            $result['car_owner'] = [
                'type'                => $owner->type,
                'name'                => $owner->type == 'Business'? 
                                            $owner->business_name : 
                                            $owner->first_name . ' ' . $owner->last_name . ' ' . $owner->mother_maiden_name,
                'rfc'                 => $owner->rfc,
                'address'             => $owner->street . ' Col. ' . 
                                         $owner->neighborhood . ' CP. ' .
                                         $owner->postal_code . ', ' . 
                                         $owner->town->town . ', ' . 
                                         $owner->state->state,
                'phone_number'        => $owner->phone_number,
                'mobile_phone_number' => $owner->mobile_phone_number,
                'email'               => $owner->email
            ];
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }

    public function getByOwner($car_owner_id)
    {
        $result = ['success' => false];
        try {
            $owner = CarOwner::find($car_owner_id);
            if ($owner === NULL)
            {
                $result['msj'] = 'No existe cliente';
                return Response::json($result);
            }
            $result['car_owner'] = [
                'type'                => $owner->type,
                'business_name'       => $owner->business_name,
                'rfc'                 => $owner->rfc,
                'first_name'          => $owner->first_name,
                'last_name'           => $owner->last_name,
                'mother_maiden_name'  => $owner->mother_maiden_name,
                'street'              => $owner->street,
                'neighborhood'        => $owner->neighborhood,
                'postal_code'         => $owner->postal_code,
                'town_id'             => $owner->town_id,
                'state_id'            => $owner->state_id,
                'phone_number'        => $owner->phone_number,
                'mobile_phone_number' => $owner->mobile_phone_number,
                'email'               => $owner->email,
                'username'            => $owner->username,
                'password'            => $owner->password
            ];
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
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