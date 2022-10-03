<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\servicios;

class ServiciosController extends Controller
{
    //

    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }


    //Listar servicios
    public function index(){
        $servicios = servicios::all();

        if(is_object($servicios)){

            $data=[
            'code' => 200,
            'status'=> 'success',
            'servicios' =>$servicios
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

     //Ver detalle servicio
     public function show($id){
        $servicios = servicios::find($id);
        
        if(is_object($servicios)){
            $data=[
                'code'=>200,
                'status'=> ' success',
                'servicio'=>$servicios
            ];
        }else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message'=>' el servicio no existe'
            ];
        }

        return response()->json($data, $data['code']);


    }

     //Guardar servicio
    public function store(Request $request){
        
        
        
        //Recoger los datos por post
        $json = $request->input('json', null);

        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if(!Empty($params_array)){
            //Validar los datos
            $validator =  \Validator::make(
                $params_array,[
                    'name' => 'required',
                    'precio' => 'required',
                    'detalle' => 'required'
                    
            ]); 

            //Guardar los servicios
            if($validator -> fails()){
                $data=[
                    'code'=>404,
                    'status'=> 'Error',
                    'message'=> 'No se ha guardado el servicio'
                ];

            }else{
                $servicio = new servicios();

                $servicio->name=$params_array['name'];
                $servicio->precio=$params_array['precio'];
                $servicio->detalle=$params_array['detalle'];
                $servicio->imagen='NULL';
                $servicio->save();
                $data = [
                    'code'=>200,
                    'status'=>'success',
                    'servicio'=>$servicio
                ];

           
            }
        }else{
            $data=[
                'code' => 404,
                'status' => 'error',
                'message' => 'No has enviado ningun servicio'
            ];
        }

        //Devolver resultado
        return response()->json($data, $data['code']);

    }

    //Actualizar servicio
    public function update($id,Request $request){
        //Recoger datos por post
        $json=$request->input('json', null);

        $params_array =json_decode($json, true);
        
        if(!empty($params_array)){

            //Validar datos
            $validate = \Validator::make(
                $params_array, [
                    'name' => 'required',
                    'precio' => 'required',
                    'detalle' => 'required'
                ]
            );
            //Quitar lo que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['imagen']);
            unset($params_array['create_at']);
        
            //Actualizar el servicio
            $servicio = servicios::where('id', $id)->update($params_array);

            $data = [
                'code' => 200,
                'status'=> 'success',
                'servicios' => $params_array
            ];
        }else{

            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No has enviado ninguna categoria'
            ];


        } 
        //Devolver respuesta
        return response()->json($data, $data['code']);
    }

    //Subir imagen
    public function upload(Request $request){
        //Recoger los datos de la peticion
        $imagen=$request->file('file1');

        //Validacion Imagen
        $validate = \Validator::make($request->all(),[
            'file1' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        //Guardar imagen
        if(!$imagen || $validate->fails()){
            $data = array (
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            );

        }else{
            $imagen_name = time().$imagen->getClientOriginalName();
            \Storage::disk('servicios')->put($imagen_name, \File::get($imagen));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name

            );

        }

        return response()->json($data, $data['code']);

    }






}
