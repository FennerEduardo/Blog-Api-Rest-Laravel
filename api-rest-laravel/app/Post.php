<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //Se crea propiedad para indicar la tabla de la DB
    protected $table = 'posts';
    
    //Se agrega método para las relaciones de los posts con los usuarios
    public function user() {
        //se hace la relación con el modelo de los usuarios de muchos a uno
        return $this->belongsTo('App\User', 'user_id');
    }
    
    
    //Se agrega método para las relaciones de los posts con las categorias
    public function category() {
        //se hace la relación con el modelo de los usuarios de muchos a uno
        return $this->belongsTo('App\Category', 'category_id');
    }
}
