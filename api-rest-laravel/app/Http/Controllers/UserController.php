<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Incluir el modelo de Usuario
use App\User;

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
                        'email' => 'required|email|unique:users', //Comprobar sí el usuario ya existe (duplicado)
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
                $pwd = hash('sha256', $params->password);
                
                //Crear el usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';
                
                //Guardar el usuario
                
                $user->save();
                
                //Crear arreglo con los datos que van a ir en el objeto JSON
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'el usuario se ha creado correctamente',
                    'user' => $user
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
        
        //Crear un obejto de la librería JWT para la autenticación de usuarios
        $jwtAuth = new \JwtAuth();
        
        //Recibir los datos del post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        
        //Validar los datos recibimos
         $validate = \Validator::make($params_array, [
                        
                        'email' => 'required|email', 
                        'password' => 'required'
            ]);
            //Comprobar sí hay errores y enviar respuesta
            if ($validate->fails()) {
                
                //Validación fallida
                
                //Crear arreglo con los datos que van a ir en el objeto JSON
                $signup = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'el usuario no se ha podido identificar',
                    'errors' => $validate->errors()
                );
            }else{
                
                //cifrar la contraseña
                $pwd = hash('sha256', $params->password);
                //devolver el token o los datos
                //Usar método del helper
                $signup = $jwtAuth->signup($params->email, $pwd);
                
                // Llega el getToken?
                if(!empty($params->gettoken)){
                    //Usar método del helper
                    $signup = $jwtAuth->signup($params->email, $pwd, true);
                }
                
            }
       
        //Usar método del helper
        return response()->json($signup, 200);
    }

    //Método para actualizar los datos del usuario
    public function update(Request $request) {
        //Obtener el token
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        
        //Comprobar sí el token llega correctamente
        if($checkToken){
            echo "<h1>Login correcto</h1>";
        }else{
            echo "<h1>Login Incorrecto</h1>";
        }
        
        die();
    }
}
