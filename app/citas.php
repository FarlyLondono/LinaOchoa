<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class citas extends Model
{
    //
    //relacion de 1 a muchos e inversa con usuarios
    public function user(){
        return $this->belongto('App\user', 'user_id');
    }

    //relacion de 1 a muchos e inversa con usuarios
    public function servicios(){
        return $this->belongto('App\servicios', 'servicio_id');
    }
}
