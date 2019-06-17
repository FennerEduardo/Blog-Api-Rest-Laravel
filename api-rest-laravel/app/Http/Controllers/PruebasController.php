<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PruebasController extends Controller
{
    //Método de prueba
    public function index() {
        $titulo = 'Animales';
        $animales = ['Perro', 'Gato', 'Tigre']; // e crea array de prueba
        
        return view('pruebas.index', array( // se retorna la vista que va a usar el método y se le pasa la variable
            'animales' => $animales,
            'titulo' => $titulo 
        ));
    }
}
