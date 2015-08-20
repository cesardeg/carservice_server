<?php


class UserController extends Controller
{
    public function login()
    {
        $input = Input::all(); 
        $result = ['success' => 0];
        if (isset($input['username']) && isset($input['password']))
        {   
            $user = User::where('username', $input['username'])
                        ->where('password', $input['password'])
                        ->where('type', 1)->get();
            if (count($user) != 0)
            {
                $result['success'] = 1;
                $user[0]->client_id = $user[0]->workshop->client->id;
                unset($user[0]->workshop);
                unset($user[0]->password);
                unset($user[0]->type);
                $result['user'] = $user[0];
            }
        }   
        return Response::json($result);
    }
}

?>