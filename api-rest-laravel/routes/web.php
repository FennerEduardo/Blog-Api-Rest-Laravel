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

//Cargando clases para usar middleware en las rutas
use  \App\Http\Middleware\ApiAuthMiddleware;

//RUTAS DE PRUEBA
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

//Ruta para probar el ORM
Route::get('/test-orm', 'PruebasController@testOrm');

// RUTAS DEL API

    /*

     * GET: Conseguir datos o recursos
     * POST: Guardar datos o recursos, hacer lógica desde un formulario
     * PUT: Actualziar datos o recursos
     * DELETE: Eliminar datos o recursos
     * 
     * Un API, solo usa GET and POST.
     * Un API RestFull Usa los cuatro métodos
     */

    //Rutas de prueba para controladores
    # Route::get('/usuario/pruebas', 'UserController@pruebas');
    # Route::get('/entrada/pruebas', 'PostController@pruebas');
    # Route::get('/categoria/pruebas', 'CategoryController@pruebas');
    
    // Rutas del controlador de Usuarios
    Route::post('/api/register', 'UserController@register');
    Route::post('/api/login', 'UserController@login');
    Route::put('/api/user/update', 'UserController@update');
    Route::post('/api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
    Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
    Route::get('/api/user/detail/{id}', 'UserController@detail');

    //Rutas del controlador de Categorias Rutas tipos Resource
    Route::resource('/api/category', 'CategoryController');

