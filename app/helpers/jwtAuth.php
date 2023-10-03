<?php

namespace App\helpers;

use Firebase\JWT\JWT;
use Illuminate\support\facades\DB;
use App\user;

class jwtAuth{

    public $key;

    public function __construct(){
        $this->key='esto_es_una_clave_secreta_99887766';
    }


    //FUNCION PARA VALIDAR EL INGRESO DEL USUARIO LOGIN

    public function signup($email, $password, $getToken=null){

        //Buscar si existe el usuario con credenciales
        $user = user::where([
            'email' => $email,
            'password' => $password
        ])->first();

        //Comprobar si son correctos(objeto)
        $signup=false;
        if(is_object($user)){
            $signup=true;
        }//Generar Token con los datos
        if($signup){
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'role' => $user->role,
                'iat' => time(),
                //tiempo logueo
                //'exp' => (time() + 60) // Caduca en 1 minuto
                //'exp' => (time() + (60 * 60)) // Caduca en 1 hora
                //'exp' => (time() + (20 * 60)) // Caduca en 20 minutos 
                'exp' => time()+(7*24*60*60)//una semana
            );
            $JWT = JWT::encode($token, $this->key, 'HS256');

            $decode=JWT::decode($JWT, $this->key, ['HS256']);

            if(is_null($getToken)){
               return $JWT;
            }else{
               $data =$decode;
            }

            //Devolver los datos de codificados o el token, en funcion de un parametro
        }else{
            $data = array(
                'status' => 'error',
                'message' => 'Login incorrecto.'
            );

        }

        return $data;

            
        
        
        
    }

    // FUNCUION PARA CHEQUEAR TOKEN
    public function checkToken($jwt, $getIdentity=false){
        $auth=false;
  
        try{
        $jwt=str_replace('"', '', $jwt);
        
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);
  
        }catch(\UnexpectedValueException $e){
           $auth=false;
        }catch(\DomainException $e){
           $auth=false;
        }
  
        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
              $auth=true;
        }else{
           $auth=false;
        }
  
        if($getIdentity){
           return $decoded;
        }
  
        return $auth;
        
    }







}






?>