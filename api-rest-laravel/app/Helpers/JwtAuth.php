<?php
//Cargar las librerías, modelo o controladores necesarios para que funcione el Helper

// crear name space
namespace App\Helpers;

//Cargar librería de JWT
use Firebase\JWT\JWT;
//cargar libreria de base de datos de Laravel
use Illuminate\Support\Facades\DB;
//Cargar modelo-Entidad de usuario
use App\User;

//Creación de la clase JWT
class JwtAuth{
    //definir propiedades
    public $key;
    
    public function __construct(){
        $this->key = 'esto_es_una_clave_super_secreta-99887766';
    }


    public function signup($email, $password, $getToken = null) {
        
        //Buscar sí existe el usuario con sus credenciales
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();
        
        //Comprobar si las credenciales son correctas(objeto)
        $signup = false;
        if(is_object($user)){
            $signup = true;
        }
        //Generar el token con los datos del usuario identificado
        if($signup){
            
            $token = array(
                'sub'       =>      $user->id,
                'email'     =>      $user->email,
                'name'      =>      $user->name,
                'surname'   =>      $user->surname,
                'iat'       =>      time(),
                'exp'       =>      time() + (7 * 24 * 60 * 60)
            );
            
            //Usar la libería para crear un objeto que codifique el token
            $jwt = JWT::encode($token, $this->key, 'HS256');
            
            //Decodificar el token para enviarlo como respuesta 
            $decoded =JWT::decode($jwt, $this->key, ['HS256']);
            
            //Devolver los datos decodificados o el token, en función de un parametro
            if(is_null($getToken)){
                $data =  $jwt; // retorna el token
            }else{
                $data = $decoded; //retorno del token decodificado
            }
            
        }else{
            $data = array(
                'status' => 'error',
                'message' => 'Login Incorrecto'
            );
        
        }
        
        
        return $data;
    }
    
    //Método para validar el token que llega del cliente
    public function checkToken($jwt, $getIdentity= false) {
        $auth = false;
        
        try{
            //Eliminar las comillas del token
            $jwt = str_replace('"', '', $jwt);
            //Decodificar el token con ayuda de la librería JWT
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }
        
        //Comprobando que el token no llega vacío, que es un objeto y que trae el id del usuario
        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }
        
        //Si se obtiene la identificación positiva del usuario se devuelven los datos dle mismo
        if ($getIdentity) {
            return $decoded;
        }
        
        //Retornar la validación del token, si es true la autenticación es valida y sí el false no lo es
        return $auth;
    }
}