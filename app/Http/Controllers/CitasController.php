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
        'citas.observaciones',
        'citas.estado')
        ->where('estado','=',1)
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
                //'FechaServicio'   => 'required',
                'FechaServicio'  => ['required', 'date','after_or_equal:now', 'unique:citas,FechaServicio', function ($attribute, $value, $fail) {
                    // Verificar que la fecha sea desde el día actual en adelante
                    $currentDate = now();
                    if (strtotime($value) < strtotime($currentDate)) {
                        $fail('La fecha de servicio debe ser desde el día actual en adelante.');
                    }
                }],
                
            ]
        );
        if($validate->fails()){

            $validations = json_decode($validate->errors(), true);

            if(isset($validations['FechaServicio'])) {
                $data = array( //La validacion ah fallado!!
                    'status' => 'errorFecha',
                    'code' => 400,
                    'message' => 'Revisa la fecha de creacion, ya esta disponible!!',
                );
            }else{
                //La validacion ah fallado!!
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'la cita no se ha creado revisa los datos',
                'errors' => $validate->errors()
            );


            }

            return response()->json($data, $data['code']);

        }else{

            //Guardar el citas
            $citas = new citas();
            $citas->user_id=$user->sub;
            $citas->servicio_id= $request->servicio_id;
            $citas->FechaServicio= $request['FechaServicio'];
            $citas->observaciones= $request['observaciones'];
            $citas->estado=1;
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

        //sacar usuario identificado
        //$user = $this->getIdentity($request);


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
        //unset($request['user_id']);
        unset($request['create_at']);

        //Conseguri usduario identificado
        $user = $this->getIdentity($request);

        //Buscar el registro a actualizar
        $citas = citas::where('id', $id)
        //->where('user_id', $user->sub)
        ->first();

        


        if(!empty($citas) && is_object($citas)){
                
            //Actualizar registro en concreto
            //$citas->update($request->all());
            $citas->user_id = $user->sub;
            $citas->estado = 2;
            $citas->save();
                    
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
