<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
         //recogemos token de autorizacion
         $token = $request ->header('Authorization');
         //iniciamos objeto
         $jwtAuth=new \jwtAuth();
         //chequeamos si el token es correcto
         $checkToken = $jwtAuth->checkToken($token);
 
        if($checkToken){
        return $next($request);
        }else{
            $data = array (
                'code' => 400,
                'status' => 'error',
                'message' => 'Usuario no identificado'
            );
        }

        return response()->json($data, $data['code']);
    }
}
