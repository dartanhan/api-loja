<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $token =  JWTAuth::getToken();
        try {
            $user = JWTAuth::authenticate($token);
            //$user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status' => 'Token é inválido'],404,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status' => 'O token está expirado'], 500,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }else{
                return response()->json(['status' => 'Token de autorização não encontrado'],404,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }
        return $next($request);
    }
}
