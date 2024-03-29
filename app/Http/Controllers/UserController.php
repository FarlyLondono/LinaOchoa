<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\helpers\jwtAuth;
use Carbon\Carbon;
use App\User;



class UserController extends Controller
{

//prueba
public function prueba(){

    $carbonDate = Carbon::parse("America/Bogota");
    $fechaDia = $carbonDate->format("d-m-Y H:i:s");
    return $fechaDia;
}

//LISTAR USUARIOS
public function index(Request $request) {
        //CONSULTAR LOS DATOS PARA MOSTRARLOS

        
            $User = User::all();
            return response()->json([
                'code' => 200,
                'status'=> 'success',
                'User' =>$User
            ]);
       
}


//REGISTRAR administrador
public function registerAdmin(Request $request) {
        //return "accion de resgistro usuario";
        
        //Recoger los datos del usuario menos el passwor
        $params_array = array_map('trim', $request->except('password'));

        //Validar datos
        $validate = Validator::make(
            $request->all(), [
            'document' => 'required|unique:users',
            'name' => 'required|alpha',
            'surname' => 'required|alpha',
            'email' => 'required|email|unique:users',//Comprobar si existe el usuario
            'password' => 'required'
        ]);
            //Limpiar datos!!
            //$params_array = array_map('trim', $params_array);
            if($validate->fails()){

                $validations = json_decode($validate->errors(), true);


                    if(isset($validations['email'])) {
                        $data = array( //La validacion ah fallado!!
                            'status' => 'errorEmail',
                            'code' => 400,
                            'message' => 'Revisa el correo, puede que ya exista!',
                        );
                    }else{
                    //La validacion ah fallado!!
                    $data = array(
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'el usuario no se a creado',
                        'errors' => $validate->errors()
                    );
                }  
                
                return response()->json($data, $data['code']);

            }
            
            
            try {

                        //validacion pasada correctamente!!
                        
                        //Cifrar contraseña
                        $pwd=hash('sha256', $request->password);

                        $carbonDate = Carbon::parse("America/Bogota");
                        $fechaDia = $carbonDate->format("d-m-Y H:i:s");

                        //var_dump($fechaDia);

                        //crear el usuario y guarda en BD!!
                        $user=new User();
                        $user->document = $params_array['document'];
                        $user->name=$params_array['name'];
                        $user->surname=$params_array['surname'];
                        $user->email=$params_array['email'];
                        $user->password=$pwd;
                        $user->role = $params_array['role'];
                        $user->created_at=$fechaDia;
                        $user->state = 'inactivo';



                        $user->save();
                        

                        $data = array(
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'el usuario se a creado correctamente',
                            'user' => $user
                        );

                }catch (Exception $e) {
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'error inesperado!!',
                        'errors' => $e
                    );
                }

        return response()->json($data, $data['code']);

}

 //registro de usuario
public function registerUser(Request $request) {

            $validate = Validator::make(
                $request->all(), [
                'document' => 'required|unique:users',
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users',//Comprobar si existe el usuario
                'password' => 'required'
            ]
            );

            if($validate->fails()){

                $validations = json_decode($validate->errors(), true);


                    if (isset($validations['email'])) {
                        $data = array( //La validacion ah fallado!!
                            'status' => 'errorEmail',
                            'code' => 400,
                            'message' => 'Revisa el correo, puede que ya exista!',
                        );
                    }elseif(isset($validations['document'])) {
                        $data = array( //La validacion ah fallado!!
                            'status' => 'errorDocument',
                            'code' => 400,
                            'message' => 'Revisa el documento, puede que ya exista!',
                        );
                    }else{
                    //La validacion ah fallado!!
                    $data = array(
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'el usuario no se a creado',
                        'errors' => $validate->errors()
                    );
                }  
                
                return response()->json($data, $data['code']);

            }

            try {
                $params_array = array_map('trim', $request->except('password'));
                $pwd = hash('sha256', $request->password); //Cifrar contraseñas!!
    
                //crear el usuario!!
                $user = new User();
                $user->document = $params_array['document'];
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'usuario';
                $user->state = 'activo';
    
                //guardar el usuario!!
                $user->save();
    
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'el usuario se ha creado correctamente!!',
                    'data' => $params_array
                );
            } catch (Exception $e) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'error inesperado!!',
                    'errors' => $e
                );
            }
    
            return response()->json($data, $data['code']);





}


//Login usuario
public function login(Request $request){

    $jwtAuth = new jwtAuth();
        
    //Validar datos
    $validate = Validator::make(
        $request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        

       
        if($validate->fails()){
            //$validations = json_decode($validate->errors(), true);

                        //La validacion ah fallado!!
                            $data = array( //La validacion ah fallado!!
                                'status' => 'errorPassword',
                                'code' => 400,
                                'message' => 'Revisa tus datos',
                            );
                
        
                return response()->json($data, $data['code']);
        }

        try {
            //Cifrar la contraseña
            $pwd = hash('sha256', $request->password);

            //devolver token o datos
            $signup = $jwtAuth ->signup($request->email, $pwd);

        if(!empty($request->gettoken)){
            $signup = $jwtAuth ->signup($request->email, $pwd, true);
        }

        return response()->json($signup, 200);


        }catch (Exception $e) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'error inesperado!!',
                'errors' => $e
            );
        }

        return response()->json($signup, $signup['code']);
        

}



//Actualizar usuario
public function update(Request $request){
        //Comprobar si el usuario esta identificado
        
        //recogemos token de autorizacion
        $token = $request ->header('Authorization');
        //iniciamos objeto
        $jwtAuth=new jwtAuth();
        //chequeamos si el token es correcto
        $checkToken = $jwtAuth->checkToken($token);


        if($checkToken){
            //Actualizar usuario
              
            //Recoger datos por post
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            //sacar usuario identificado
            $user = $jwtAuth->checkToken($token, true);

            //Validar datos
            $validate = \Validator::make(
                $params_array, [
                    'name' => 'required|alpha',
                    'surname' => 'required',
                    'email' => 'required|email|inique:users,'.$user->sub //Comprobar si el usuario existe
                ]
            );

            //Quitar los campos que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['document']);
            unset($params_array['state']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['create_at']);
            unset($params_array['remember_token']);

            //Actualizar usuario en BD
            $user_update = User::where('id', $user->sub)->update($params_array);

            //Devolver el resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'change' => $params_array
            );
        }else{

            $data = array (
                'code' => 400,
                'status' => 'error',
                'message' => 'Usuario no identificado'
            );

        }
        return response()->json($data, $data['code']);

}


//Ver detalle de usuario
public function detail($id){
        $user = User::find($id);

        if(is_object($user)){
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' =>$user
            );
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no existe'
            );
        }

        return response()->json($data, $data['code']);

}





}
