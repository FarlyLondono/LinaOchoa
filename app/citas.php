<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class citas extends Model
{
    //
    protected  $table='citas';

    protected $fillable = [
        'servicio_id','FechaServicio',
        ];


    //relacion de 1 a muchos e inversa con usuarios
    public function user(){
        return $this->belongsTo('App\user', 'user_id');
    }

    //relacion de 1 a muchos e inversa con usuarios
    public function servicios(){
        return $this->belongsTo('App\servicios', 'servicio_id');
    }
}
