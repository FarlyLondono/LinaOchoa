<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\servicios;

class ServiciosController extends Controller
{
    //

    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }



/*public function show($id) {
        $servicio = servicios::findOrFail($id); // Suponiendo que tengas un modelo "servicios"
        return view('show', compact('servicio'));
}*/



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
        $servicios = servicios::findOrFail($id);
        //dd($servicios);
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

    $imagen=$request->file('imagen');

    //Validar los datos   
    $validator =  Validator::make(
        $request->all(),[  //Recoger los datos por post
            'name' => 'required|unique:servicios',
            'precio' => 'required',
            'detalle' => 'required',
            'imagen' => 'required|image|mimes:jpg,jpeg,png,gif'
            
    ]);

        if(!$imagen || $validator -> fails()){

            $validations = json_decode($validator->errors(), true);

            if(isset($validations['name'])) {
                $data = array( //La validacion ah fallado!!
                    'status' => 'errorName',
                    'code' => 400,
                    'message' => 'el nombre del servicio ya existe!',
                );
            }else{
            //La validacion ah fallado!!
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'el servicio no se a creado',
                'errors' => $validator->errors()
            );
            }  

            return response()->json($data, $data['code']);
        }
    
    try{

        //$params_array = array_map('trim', $request);

        $imagen_name = time().$imagen->getClientOriginalName();
        Storage::disk('servicios')->put($imagen_name, File::get($imagen));

        //Guardar los servicios
        $servicio = new servicios();

        $servicio->name=$request['name'];
        $servicio->precio=$request['precio'];
        $servicio->detalle=$request['detalle'];
        $servicio->imagen=$imagen_name;
        $servicio->save();
        $data = [
            'code'=>200,
            'status'=>'success',
            'servicio'=>$servicio
        ];

   
    }catch (Exception $e) {
        $data = array(
            'status' => 'error',
            'code' => 400,
            'message' => 'error inesperado!!',
            'errors' => $e
        );
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
            $validate = Validator::make(
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

//Subir imagen, se realiza la carga en el metodo registro (store)
public function upload(Request $request){
        //Recoger los datos de la peticion
        $imagen=$request->file('file1');

        //Validacion Imagen
        $validate = Validator::make($request->all(),[
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
            Storage::disk('servicios')->put($imagen_name, File::get($imagen));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $imagen_name

            );

        }

        return response()->json($data, $data['code']);

}






}
