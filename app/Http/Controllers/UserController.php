<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller
{


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

    //REGISTRAR
    public function register(Request $request) {
        //return "accion de resgistro usuario";
        
        //Recoger los datos del usuario
        $json =$request->input('json',null);

        $params = \GuzzleHttp\json_decode($json);
        $params_array = \GuzzleHttp\json_decode($json,true);

        //Validar entrada de datos
        if(!empty($params) && !empty($params_array)){

            //Limpiar datos!!
            $params_array = array_map('trim', $params_array);

                //Validar datos
                $validate = \Validator::make(
                    $params_array, [
                    'name' => 'required|alpha|unique:users',
                    'surname' => 'required|alpha',
                    'email' => 'required|email|unique:users',//Comprobar si existe el usuario
                    'password' => 'required'
                ]);

                    if($validate->fails()){

                        //La validacion ah fallado!!
                        $data = array(
                            'status' => 'error',
                            'code' => 404,
                            'message' => 'el usuario no se a creado',
                            'errors' => $validate->errors()
                        );


                    }else{

                        //validacion pasada correctamente!!
                        
                        //Cifrar contraseña
                        $pwd=hash('sha256', $params->password);

                        //crear el usuario y guarda en BD!!
                        $user=new User();
                        $user->name=$params_array['name'];
                        $user->surname=$params_array['surname'];
                        $user->email=$params_array['email'];
                        $user->password=$pwd;
                        $user->role = 'role_user';

                        $user->save();
                        

                        $data = array(
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'el usuario se a creado correctamente',
                            'user' => $user
                        );

                }


        }else{

            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos'
            );


        }

        return response()->json($data, $data['code']);

    }

    //Login usuario
    public function login(Request $request){
        $jwtAuth = new \jwtAuth();

        //Recibir datos por post
        $json = $request->input('json', null);

        $params= json_decode($json);
        $params_array=json_decode($json, true);
        //Validar datos
        $validate = \Validator::make(
            $params_array , [
                'email' => 'required|email',
                'password' => 'required'
            ]);

        if($validate->fails()){
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se a podido loguear',
                'errors' => $validate->errors()
            );
        }else{

            //Cifrar la contraseña
            $pwd = hash('sha256', $params->password);

            //devolver token o datos
            $signup = $jwtAuth ->signup($params->email, $pwd);

        if(!empty($params->gettoken)){
            $signup = $jwtAuth ->signup($params->email, $pwd, true);
        }

        }

        return response()->json($signup, 200);

    }

    //Actualizar usuario
    public function update(Request $request){
        //Comprobar si el usuario esta identificado
        
        //recogemos token de autorizacion
        $token = $request ->header('Authorization');
        //iniciamos objeto
        $jwtAuth=new \jwtAuth();
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
