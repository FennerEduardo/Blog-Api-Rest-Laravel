<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller {

    public function pruebas(Request $request) {
        return "Acción de pruebas de UserController";
    }

    //Método de registro
    public function register(Request $request) {

        //Recoger los datos del usuario por post
        $json = $request->input('json', null); //Almacena en un string el objeto JSON
        
        //Decodificar el JSON
        $params = json_decode($json); //Se obtiene un Objeto PHP con los datos del objeto Json
        $params_array = json_decode($json, true); //Se obtiene un array PHP con los datos del objeto Json
        
        // solo se hace la validación si el objeto o el array no viene vacío
        if(!empty($params) && !empty($params_array)){
            //Limpiar los datos quitar espacios en blanco en las cadenas 
            $params_array = array_map('trim', $params_array);

            // Validar datos
            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'surname' => 'required|alpha',
                        'email' => 'required|email',
                        'password' => 'required'
            ]);
            //Comprobar sí hay errores y enviar respuesta
            if ($validate->fails()) {
                
                //Validación fallida
                
                //Crear arreglo con los datos que van a ir en el objeto JSON
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'el usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            } else{
                
                //Validación pasada corectamente
                
                
                //Cifrar la contraseña
                //Comprobar sí el usuario ya existe (duplicado)
                //Crear el usuario
                
                
                //Crear arreglo con los datos que van a ir en el objeto JSON
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'el usuario se ha creado correctamente'                
                );

            } 
        }else {
            $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Los datos enviados no son correctos'
                );
        }

        
        //Retornar el objeto JSON, con el método json(), se convierte el array en un objeto JSON
        return response()->json($data, $data['code']);
    }

    //Método de login
    public function login(Request $request) {
        return "Acción de Login de usuarios";
    }

}
