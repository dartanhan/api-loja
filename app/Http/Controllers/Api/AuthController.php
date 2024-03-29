<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Info(
 *   title="API Laravel Swagger Documentação - KN Cosméticos",
 *   version="1.0.0",
 *   contact={
 *     "email": "dartanhan.lima@gmail.com"
 *   }
 * )
 *
 */
class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        // $credentials = $request->only(['login', 'password']);

        // if (!$token = auth('api')->attempt($credentials)) {
        //     return response()->json(['error' => 'Acesso não autorizado!'], 401);
        // }

        //return $this->respondWithToken($token);
        $loginField = $request->input('login');
        $password = $request->input('password');

        // Verifica se o loginField parece ser um email
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            // Se for um email, tenta fazer login usando email
            $credentials = ['email' => $loginField, 'password' => $password];
        } else {
            // Se não for um email, tenta fazer login usando o nome de usuário
            $credentials = ['login' => $loginField, 'password' => $password];
        }

        // Tenta fazer login com as credenciais fornecidas
        if (!$token = auth('api')->attempt($credentials)) {
                 return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        // Se o login for bem-sucedido, retorna o token de autenticação
        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        auth('api')->logout();

        return response()->json(['message' => 'Saiu com sucesso!'], 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }


    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 30, //+10 horas
            'status_token' => 200
        ]);
    }

    public function register(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            //'login' => 'required|string|login|min:6|max:255|unique:users'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }


        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'login' => $request->get('login')
        ]);


        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }
}

