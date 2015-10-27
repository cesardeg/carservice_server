<?php


class UserController extends Controller
{
    public function login()
    {
        $input = Input::all(); 
        $result = ['success' => false];
        try
        {
            $user = User::
                  where('username', $input['username'])
                ->where(DB::raw('BINARY `password`'), $input['password'])->first();
            if ($user !== NULL)
            {
                $result['user_id'] = $user->id;
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

    public function loginAdmin()
    {
        $input = Input::all(); 
        try
        {
            $user = User::
                  where('username', $input['username'])
                ->where(DB::raw('BINARY `password`'), $input['password'])
                ->where('type', 'admin')->first();
            if ($user === NULL)
            {
                return View::make('login.login',['msj' => 'nombre de usuario y/o contraseña incorrectos']);
            }
            Auth::loginUsingId($user->id);
        }
        catch(Exception $e)
        {
            return View::make('login.login',['msj' => $e->getMessage()]);
        } 
        return Redirect::to('operators_list');
    }


    public function user($user_id)
    {
        $result = ['success' => false];
        try {
            $user = User::find($user_id);
            if ($user === NULL)
            {
                $result['msj'] = 'No existe el usuario';
                return Response::json($result);
            }
            $result['user'] = [
                'name'          => $user->first_name . ' ' . $user->last_name . ' ' . $user->mother_maiden_name,
                'client_name'   => $user->workshop->client->name,
                'workshop_name' => $user->workshop->name
            ];
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);
    }
}

?>