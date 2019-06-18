<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //Se crea propiedad para indicar la tabla de la DB
    protected $table = 'categories';
    
    //Se agrega método para las relaciones de las categorías con los posts
    public function posts() {
        //se hace la relación con el modelo de los posts de uno a muchos
        return $this->hasMany('App\Post');
    }
}
