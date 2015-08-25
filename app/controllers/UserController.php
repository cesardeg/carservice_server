<?php


class UserController extends Controller
{
    public function login()
    {
        $input = Input::all(); 
        $result = ['success' => false];
        try
        {
            $user = User::where('username', $input['username'])->
                where('password', $input['password'])->
                where('type', 1)->get();
            if (count($user) != 0)
            {
                $result['user_id'] = $user[0]->id;
                $result['success'] = true;
            }
            else
            {
                $result['msj'] = "nombre de usuario y/o contraseña incorrectos";
            }

        }
        catch(Exception $e)
        {
            $result['msj'] = $e->getMessage();
        } 
        return Response::json($result);
    }

    public function user($user_id)
    {
        $result = ['success' => false];
        $user = User::find($user_id);
        if ($user === NULL)
        {
            $result['msj'] = 'No existe el usuario';
            return Response::json($result);
        }
        $userdata['name'] = $user->first_name . ' ' . $user->last_name . ' ' . $user->mother_maiden_name;
        $userdata['client_name']   = $user->workshop->client->name;
        $userdata['workshop_name'] = $user->workshop->name;
        $result['success'] = true;
        $result['user'] = $userdata;
        return Response::json($result);
    }
}

?>