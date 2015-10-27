<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OperatorController
 *
 * @author Arellano
 */
class OperatorController extends BaseController{
    //put your code here
	 
    public function operators_list()
    {
        $array = [];
        if (Auth::user() !== NULL) {
            $array = User::
              where('workshop_id', Auth::user()->workshop_id)->get()
            ->map(function($operator){
                return [
                    'id'         => $operator->id,
                    'first_name' => $operator->first_name,
                    'last_name'  => $operator->last_name,
                    'mother_maiden_name' => $operator->mother_maiden_name,
                    'username'   => $operator->username,
                    'cell_phone' => $operator->cell_phone,
                    'type'       => $operator->type,
                ];
            });
        }
        
        return View::make('operators.operators_list',['operators'=>$array]);         
    }         
    
    public function store($operatorId)
    {
        $result = ['success' => false];
        $operator = null;
        try {
            $input = Input::All();
            if($operatorId == 0) {
                $operator       = new User();
                $operator->type = 'operator';
                $operator->workshop_id = Auth::user()->workshop_id;
            } else {
                $operator = User::find($operatorId); 
                if($operator === null) {
                    $result['msj'] = 'No existe operador';
                    return Response::json($result);                
                }                
            }
            $operator->fill($input);
            $operator->password    = $input['password'];
            $operator->username    = $input['username'];
            $operator->save();
            $result['success'] = true;
        } catch (Exception $e) {
            if (contains($e->getMessage(), '1062') &&
                contains($e->getMessage(), 'key \'username\'') )
            {
                $result['msj'] = 'El nombre de usuario ' . $input['username'] . ' no esta disponible';
            }
            else {
                $result['msj'] = $e->getMessage();
            }
        }
        return Response::json($result);
    }
    
    public function delete($operatorId)
    {
        $result = ['success' => false];
        try {
            $operator = User::find($operatorId);
            if( $operator === NULL ) {
                $result['msj'] = 'No existe operador';
                return Response::json($result);     
            }
            if ($operator->type == 'admin'){
                $result['msj'] = 'No se puede eliminar un administrador';
                return Response::json($result);
            }
            $operator->delete();
            $result['success'] = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }
        return Response::json($result);          
    }
    
    public function get($operatorId)
    {
        $result = ['success' => false];
        try {
            $operator = User::find($operatorId);
            if ($operator === null) {
                $result['msj'] = 'Usuario no encontrado';
                return Response::json($result);
            }
            $result['operator'] = $operator;
            $result['success']  = true;
        } catch (Exception $e) {
            $result['msj'] = $e->getMessage();
        }         
        return Response::json($result);      
    }
}

function contains($haystack, $needle)
{
    return strpos($haystack, $needle) !== false;
}
