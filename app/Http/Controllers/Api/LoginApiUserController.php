<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class LoginApiUserController extends Controller
{
    protected  $request, $usuario;

    public function __construct(Request $request, Usuario $usuario){
        $this->request = $request;
        $this->usuario = $usuario;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store()
    {
       // dd($this->request->header('authorization'));
        //$this->usuario = $this->usuario::all();

        try {

        $validator = Validator::make($this->request->all(), [
            'login' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return Response::json(array('success' => false, 'status' => 400, 'message' => 'Informe o login e senha!!'), 400);
        }


        $this->usuario = $this->usuario::where(['login' =>  $this->request->input('login')])->get();
            if($this->usuario->isNotEmpty()){
                if($this->usuario[0]->login === $this->request->input('login')){
                    if(Hash::check($this->request->input('password'), $this->usuario[0]->senha)){
                        if($this->usuario[0]->status === 1){

                            $data['login'] = $this->usuario[0]->login;
                            $data['nome'] = $this->usuario[0]->nome;
                            $data['sexo'] = $this->usuario[0]->sexo;
                            $data['loja_id'] = $this->usuario[0]->loja_id;
                            $data['admin'] = $this->usuario[0]->admin == 0 ? false : true;
                            $data['success'] = true;
                            $data['token'] = $this->request->header('authorization');
                            $data['database'] = $this->usuario[0]->database;


                            return Response()->json($data,200);

                        }else{
                            return Response::json(array('success' => false, 'status' => 206, 'message' => 'Usuário bloqueado!!!'), 206,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        }
                    }else{
                        return Response::json(array('success' => false, 'status' => 206, 'message' => 'Usuário ou Senha inválidos!!!!'), 206,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    }
                }else{
                    return Response::json(array('success' => false, 'status' => 206, 'message' => 'Usuário ou Senha inválidos!!!'), 206,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }

            }else{
                return Response::json(array('success' => false, 'status' => 206, 'message' => 'Usuário ou Senha inválidos!!!'), 206,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }


        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'status' => 500), 500,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show()
    {
        $this->usuario = $this->usuario::all();
        return Response()->json($this->usuario);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }
}
