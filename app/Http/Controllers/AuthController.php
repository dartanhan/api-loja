<?php

namespace App\Http\Controllers;

use App\Http\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ReCaptcha\ReCaptcha;
use function Psy\debug;


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

    function login(Request $request) {

        $secret = env('DATA_SECRET_KEY');

        $response = (new ReCaptcha($secret))->verify($request->input('g-recaptcha-response'), $request->ip());
          //  ->setExpectedHostname('127.0.0.1')
            //->setExpectedAction('homepage')

        if ($response->getScore()  < 0.5) {
            return redirect()->back()->withInput()->withErrors(['Você é considerado um Bot / Spammer!' . $response->getScore()]);
        }

        if ($response->isSuccess()) {
//             if(!filter_var($request->input("email") , FILTER_VALIDATE_EMAIL)){
//                 return redirect()->back()->withInput()->withErrors(['Login informado não é valido!']);
//             }

            // $credentials = [
            //     'email' => $request->input("email"),
            //     'password' => $request->input("password")
            // ];

            $loginField = $request->input('login');
            $password = $request->input('password');

            if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
                // Se for um email, tenta fazer login usando email
                $credentials = ['email' => $loginField,'password' => $password];
            } else {
                // Se não for um email, tenta fazer login usando o nome de usuário
                $credentials = ['login' => $loginField, 'password' => $password];
            }

            if(Auth::attempt($credentials)){
                return redirect()->route('admin.home');
            }
            return redirect()->back()->withInput()->withErrors(['Dados informados são inválidos!']);
        } else {
            //$errors = $response->getErrorCodes();
            return redirect()->back()->withInput()->withErrors(['Parece que você é um robô!']);
        }
    }

    function logout() {
        Auth::logout();

        return redirect()->route('admin.login');
    }
}
