<?php

namespace App\Http\Controllers;

use App\Http\Models\Usuario;
use GuzzleHttp\Client;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;



class AuthController extends Controller
{
    public function dashboard() {

        if(Auth::check() === true){
            $user_data = Usuario::where("user_id",auth()->user()->id)->first();

            $store_id = $user_data->loja_id;
            $isAdmin = $user_data->admin;

            if($isAdmin){
                return view('admin.dashboard',compact("user_data"));
            }else{
               // return view('admin.pdv',compact("isAdmin"));
                return redirect()->route('admin.pdv');
            }


        }

        return redirect()->route('admin.login');

    }

    function showLoginForm() {
        //return view('admin.formLogin');
        return view('admin.login');
    }

    public function login(Request $request)
    {
        // Validação básica dos campos
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // ReCaptcha v3 via Guzzle
        $secret = env('DATA_SECRET_KEY');
        $recaptcha = $request->input('g-recaptcha-response');
        $client = new Client();

        try {
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => $secret,
                    'response' => $recaptcha,
                    'remoteip' => $request->ip()
                ],
                'verify' => true
            ]);

            $body = json_decode((string)$response->getBody(), true);

            if (!isset($body['success']) || $body['success'] !== true || $body['score'] < 0.5) {
                return redirect()->back()->withInput()->withErrors([
                    'recaptcha' => 'Você é considerado um bot ou spammer! Score: ' . ($body['score'] ?? 'sem score')
                ]);
            }

            // Coloque todo acesso ao banco dentro desse try
            $loginField = $request->input('login');
            $password = $request->input('password');

            $credentials = filter_var($loginField, FILTER_VALIDATE_EMAIL)
                ? ['email' => $loginField, 'password' => $password]
                : ['login' => $loginField, 'password' => $password];

            if (Auth::attempt($credentials)) {
                return redirect()->route('admin.home');
            }

            return redirect()->back()->withInput()->withErrors(['login' => 'Dados informados são inválidos!']);

        } catch (QueryException $e) {
            Log::error('Erro ao conectar com o banco de dados: ' . $e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'login' => 'Sistema temporariamente indisponível. Tente novamente mais tarde.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro inesperado ao logar: ' . $e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'login' => 'Erro inesperado. Tente novamente mais tarde.'
            ]);
        }
    }



    function logout() {
        Auth::logout();

        return redirect()->route('admin.login');
    }
}
