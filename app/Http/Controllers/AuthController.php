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

            $store_id = $user_data->loja_id ?? null;
            $isAdmin = $user_data->admin ?? null;

            if($isAdmin){
                return view('admin.dashboard',compact("user_data"));
            }else{
                return redirect()->route('admin.pdv');
            }
        }

        return redirect()->route('admin.login');
    }

    function showLoginForm() {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        // Validação básica dos campos
        $rules = [
            'login' => 'required',
            'password' => 'required',
        ];

        // Só exige o recaptcha se as chaves reais estiverem configuradas
        $secret = env('DATA_SECRET_KEY');
        if (!empty($secret) && $secret !== 'SUA_CHAVE_SECRETA_AQUI') {
            $rules['g-recaptcha-response'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Se houver secret configurado e não for o placeholder, valida no Google
            if (!empty($secret) && $secret !== 'SUA_CHAVE_SECRETA_AQUI') {
                $recaptcha = $request->input('g-recaptcha-response');
                $client = new Client();

                $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                    'form_params' => [
                        'secret' => $secret,
                        'response' => $recaptcha,
                        'remoteip' => $request->ip()
                    ],
                    'verify' => true
                ]);

                $body = json_decode((string)$response->getBody(), true);
                $score = $body['score'] ?? 1.0; // fallback se não for reCAPTCHA v3 com score

                if (!isset($body['success']) || $body['success'] !== true || $score < 0.5) {
                    return redirect()->back()->withInput()->withErrors([
                        'recaptcha' => 'Você é considerado um bot ou spammer! Score: ' . $score
                    ]);
                }
            }

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
                'login' => 'Erro inesperado: ' . $e->getMessage()
            ]);
        }
    }

    function logout() {
        Auth::logout();

        return redirect()->route('admin.login');
    }
}
