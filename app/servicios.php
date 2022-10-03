<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class servicios extends Model
{
    //
    //relacion de uno a muchos con citas
    public function citas(){
        return $this->hasmany('App\citas');
    }
}
