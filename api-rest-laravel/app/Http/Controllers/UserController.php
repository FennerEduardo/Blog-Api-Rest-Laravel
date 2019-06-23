<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        
        //Crear un objeto de la librería JWT para la autenticación de usuarios
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
        
        //Recoger datos por Post
        $json = $request->input('json', null);
        //Decodificar el objeto Json
        #$params = json_decode($json);
        $params_array = json_decode($json, true); 
        
        
        //Comprobar sí el token llega correctamente
        if($checkToken && !empty($params_array)){
                     
            //Obtener el usuario identificado
            $user = $jwtAuth->checkToken($token, true);
            
            
            //Validar Datos  //Comprobar sí el usuario ya existe (duplicado)
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users,'.$user->sub 
            ]);
            
            //Quitar campos que no se van a actualizar
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            
            //Actualizar el usuario en el DDBB
            $user_update = User::where('id', $user->sub)->update($params_array);
            
            //Devolver array con el resultado de la actualizacion
            $data = array(
               'code' => 200,
               'status' => 'success',
               'user' => $user,
               'changes' => $params_array
           );
            
            
            
        }else{
           $data = array(
               'code' => 400,
               'status' => 'error',
               'message' => 'El usuario no está identificado'
           );
        }
        
        //Convertir la variable data en un objeto Json
        return response()->json($data, $data['code']); 
    }
    
    //Método para cargar un la imagen del avatar de usuario
    public function upload(Request $request) {
        
        //Recoger datos de las petición 
        $image = $request->file('file0');
        
        //Validar que sea una imagen
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg.jpeg,png,gif'
        ]);
        
        // Guardar la imagen
        if(!$image || $validate->fails()){
            
            //Devolver resultados negativos

            $data = array(
                   'code' => 400,
                   'status' => 'error',
                   'message' => 'Error al subir imagen'
               );
           
        } else{
               
            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));
            
           //Devolver resultados positivos
            $data = array(
               'code' => 200,
               'status' => 'success',
               'image' => $image_name
           );
        }
        //Convertir la variable data en un objeto Json
        return response()->json($data, $data['code']); 
    }
    
    //Método para obtener una imagen del disco virtual de la app
    public function getImage($filename) {
        //comprobar si la imagen existe
        $isset = \Storage::disk('users')->exists($filename);
        
        if($isset){
        
            //obtener el disco y el archivo de imagen 
            $file = \Storage::disk('users')->get($filename);
                  
            //Retornar el archivo obtenido
            return new Response($file, 200);
        
        }else {
            $data = array(
                   'code' => 404,
                   'status' => 'error',
                   'message' => 'La imagen no existe'
               );
        }
        //Convertir la variable data en un objeto Json
        return response()->json($data, $data['code']); 
    }
    
    //Método para obtener los datos del usuario logueado
    public function detail($id) {
        
        //Buscar el usuario con el id que se pasa  por parametro
        $user = User::find($id);
        
        //Comprobar sí hay un objeto en el $user
        if(is_object($user)){
            $data = array(
                   'code' => 200,
                   'status' => 'success',
                   'user' => $user
               );
        } else{
            $data = array(
                   'code' => 404,
                   'status' => 'error',
                   'message' => 'El usuario no existe'
               );
        }
        
        //Convertir la variable data en un objeto Json
        return response()->json($data, $data['code']); 
    }
}
