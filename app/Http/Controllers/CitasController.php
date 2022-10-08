<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\citas;
use App\helpers\jwtAuth;

class CitasController extends Controller
{
    //LISTAR CITAS
    public function index(){

        $citas = citas::join('users as Usuario', 'Usuario.id', '=', 'citas.user_id')
        ->join('servicios', 'servicios.id', '=', 'citas.servicio_id')
        ->select('citas.id',
        'Usuario.name as NombreUsuario',
        'servicios.name as Servicio',
        'servicios.precio',
        'citas.FechaServicio',
        'citas.observaciones')
        ->get();

        if(is_object($citas)){

            $data=[
            'code' => 200,
            'status'=> 'success',
            'citas' =>$citas
            ];
        }else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message'=>' los servicios no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //VER DETALLE CITA
    public function show($id){
        //$citas = citas::find($id)->load('servicios');
        $citas = citas::join('users as Usuario', 'Usuario.id', '=', 'citas.user_id')
        ->join('servicios', 'servicios.id', '=', 'citas.servicio_id')
        ->select('citas.id',
        'Usuario.name as NombreUsuario',
        'servicios.name as Servicio',
        'servicios.precio',
        'citas.FechaServicio',
        'servicios.detalle',
        'servicios.imagen',
        'citas.observaciones')
        ->where('citas.id','=',$id)
        ->get();

        if(is_object($citas)){

            $data = [
                'code' => 200,
                'status' => 'success',
                'servicios' => $citas
            ];

        }else{

            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La entrada no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }


    //REGISTRAR CITA
    public function store(Request $request){

        //sacar usuario identificado
        $user = $this->getIdentity($request);

        $validate = Validator::make( //Validar datos!!
            $request->all(),
            [
                'servicio_id'      => 'required',
                'FechaServicio'   => 'required',
            ]
        );
        if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se a guardado la Evaluacion, faltan datos'
            ];

        }else{

            //Guardar el citas
            $citas = new citas();
            $citas->user_id=$user->sub;
            $citas->servicio_id= $request->servicio_id;
            $citas->FechaServicio= $request['FechaServicio'];
            $citas->observaciones= $request['observaciones'];
            $citas->save();

            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $citas
            ];
        }
        return response()->json($data, $data['code']);//Devolver respuesta


    }


    
    //ACTUALIZAR CITAS
    public function update($id, Request $request){
        //Validar los datos
        $validate = Validator::make(
            $request->all(), [
            'servicio_id' => 'required',
            'FechaServicio' => 'required',
            
        ]);

        if($validate->fails()){
            $data['errors'] = $validate->errors();
            return response()->json($data, $data['code']);
        }

        //Eliminar lo que no quiero actualizar
        unset($request['id']);
        unset($request['user_id']);
        unset($request['create_at']);

        //Conseguri usduario identificado
        $user = $this->getIdentity($request);

        //Buscar el registro a actualizar
        $citas = citas::where('id', $id)
        ->where('user_id', $user->sub)
        ->first();


        if(!empty($citas) && is_object($citas)){
                
            //Actualizar registro en concreto
            $citas->update($request->all());
            
            //devolver algo
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $citas,
                'changes' => $request->all()
            ];

        }
        
        return response()->json($data, $data['code']);

    
    
    }


    //IDENTIFICAR USUARIO
    private function getIdentity($request){

            //Conseguir usuario identificado
    
            //Traemos la clase creada en provider
            $jwtauth = new \jwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtauth->checkToken($token, true);
    
            return $user;
    
    }

    






}
