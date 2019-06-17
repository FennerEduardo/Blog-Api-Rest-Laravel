<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Ruta principal
Route::get('/', function () {
    return '<h1>Hola mundo con Laravel</h1>';
});

//Ruta welcome para comprobar funcionamiento
Route::get('/welcome', function () {
    return view('welcome');
});
//Ruta de prueba con parametros
Route::get('/pruebas/{nombre?}', function($nombre = null){
    
    $texto = '<h2>Texto desde una ruta</h2>';
    $texto .= 'Nombre: '.$nombre;
    
    return view('pruebas', array(
        'texto' => $texto
    ));
});

//Ruta para una vista desde un controlador
Route::get('/animales', 'PruebasController@index');