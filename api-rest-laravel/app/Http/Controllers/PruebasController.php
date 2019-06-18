<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Cargar modelos de posts y categorías para las pruebas del ORM 
use App\Post;
use App\Category;


class PruebasController extends Controller
{
    //Método de prueba
    public function index() {
        $titulo = 'Animales';
        $animales = ['Perro', 'Gato', 'Tigre', 'panda']; // e crea array de prueba
        
        return view('pruebas.index', array( // se retorna la vista que va a usar el método y se le pasa la variable
            'animales' => $animales,
            'titulo' => $titulo 
        ));
    }
    
    //Método para probar el ORM
    public function testOrm() {
        
        /*
        //Cargar todos los post mediante un array
        $posts = Post::all();
        //Búcle para mostrar los posts
        foreach($posts as $post){
            echo '<h1>'.$post->title.'</h1>';
            echo '<span style="color: green;">'.$post->user->name.' | '.$post->category->name.'</span>';
            echo '<p>'.$post->content.'</p>';
            echo '<hr>';
        }
        */
        //Listar todas las categorías
        $categories = Category::all();
        
        foreach($categories as $category){
            echo '<h1>'.$category->name.'</h1>';
            
            //Búcle para mostrar los posts
            foreach($category->posts as $post){
                echo '<h2>'.$post->title.'</h2>';
                echo '<span style="color: green;">'.$post->user->name.' | '.$post->category->name.'</span>';
                echo '<p>'.$post->content.'</p>';
                
            }
            echo '<hr>';
        }
        
        die();
    }
}
