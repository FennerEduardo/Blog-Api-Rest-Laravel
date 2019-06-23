<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
//Usar el modelo de categorías
use App\Category;

class CategoryController extends Controller
{
    //Método constructor, se incluye el middleware para designar los métodos que requieren autenticación
    public function __construct(){
        //Se configuran los métodos que no necesitan autenticación
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    //Método para listar todas las categorías del blog
    public function index() {
        //Obtener todas las categorías
        $categories = Category::all();
        
        //Devolver la respuesta con los datos obtenidos
        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'categories'=> $categories
        ]);
    }
    
    //Método para mostrar una categoría
    public function show($id) {
        //obtener la categoría que corresponde al $id
        $category = Category::find($id);
        
        //Comprobar sí la categoría es un objeto
        if(is_object($category)){
            $data = array(
                'code'          => 200,
                'status'        => 'success',
                'category'      => $category
            );
        }else{
            $data = array(
                'code'          => 404,
                'status'        => 'error',
                'message'       => 'La categoría no existe'
            );
        }
        return response()->json($data, $data['code']);
    }
    
    //Método para crear una categoría
    public function store(Request $request) {
        // Recoger los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        if(!empty($params_array)){
            // Validar los datos
            $validate = \Validator::make($params_array, [
                'name'  => 'required'
            ]);

            // Guardar la categoría
            if($validate->fails()){
                $data = [
                    'code'          => 400,
                    'status'        => 'error',
                    'message'       => 'No se ha guardado la categoría'
                ];
            }else{
                //Guardar la categoría
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();

                $data = [
                    'code'          => 200,
                    'status'        => 'success',
                    'category'       => $category
                ];
            }
        }else{
           $data = [
                    'code'          => 400,
                    'status'        => 'error',
                    'message'       => 'No has enviado ninguna categoría'
                ]; 
        }
        //Devolver el resultado
        return response()->json($data, $data['code']);
 
    }
    
    //Método de actualización de categorías
    public function update($id, Request $request) {
        //Recoger los datos del Post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        if(!empty($params_array)){
            //Validar los datos
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);

            //Quitar lo que no se va a actualizar
            unset($params_array['id']);
            unset($params_array['created_at']);

            //Actualizar la categoría
            $category = Category::where('id', $id)->update($params_array);
            
            $data = [
                    'code'          => 200,
                    'status'        => 'success',
                    'category'       => $params_array
                ];
            
        }else{
            $data = [
                    'code'          => 400,
                    'status'        => 'error',
                    'message'       => 'No has enviado ninguna categoría'
                ]; 
        }
        
        //Devolver los datos
        //Devolver el resultado
        return response()->json($data, $data['code']);
    }
}
