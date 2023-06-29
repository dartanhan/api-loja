<?php

namespace App\Http\Controllers;

use App\Http\Models\Lojas;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class UserSystemController extends Controller
{

    protected $request,$user,$store;

    public function __construct(Request $request, Usuario $user, Lojas $store){

        $this->request = $request;
        $this->user = $user;
        $this->store = $store;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $stores = $this->store->orderBy('nome', 'ASC')->get();

        $user_data = Usuario::where("user_id",auth()->user()->id)->first();

        return view('admin.usuarios',compact('stores','user_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        try {

            foreach ($this->user::all() as $value){
                $data['id'] =  $value->id;
                $data['nome'] =  $value->nome;
                $data['login'] =  $value->login;
                $data['senha'] =  $value->senha;
                $data['status'] =  $value->status == 1 ? "ATIVO" : "INATIVO";
                $data['created_at'] =  date('d/m/Y H:i:s', strtotime($value->created_at));
                $data['updated_at'] =  date('d/m/Y H:i:s', strtotime($value->updated_at));

                $ret[] = $data;
            }

            if(empty($ret))
                return Response()->json(array('data'=>''));

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response()->json($ret);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store()
    {
       // dd('aqui');
        try {

            $validator  = Validator::make($this->request->all(), [
                        'nome' => 'required:'.$this->user->table.'|max:155',
                        'login' => 'required|unique:'.$this->user->table.'|max:15',
                        'senha' => 'required:'.$this->user->table.'|max:15',
                   ],[
                        'nome.max'=> 'Nome deve ser menos que 155 caracteres!',
                        'nome.required'=> 'Nome é obrigatório!',
                        'login.unique'  => 'Login já cadastrado!',
                        'login.required'  => 'Login é obrigatório!',
                        'login.max'=> 'Login deve ser menos que 15 caracteres!',
                        'senha.required'  => 'Senha é obrigatório!',
                        'senha.max'  => 'Senha deve ser menos que 15 caracteres!'
                    ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            $data["nome"] = $this->request->input("nome");
            $data["senha"] = Hash::make($this->request->input("senha"));
            $data["login"] = strtolower($this->request->input("login"));
            $data["status"] = $this->request->input("status");
            $data["loja_id"] = $this->request->input("loja_id");
            $data["sexo"] = $this->request->input("sexo");
            $data["admin"] = $this->request->input("admin");

            $this->user::create($data);


        } catch (Throwable $e) {
            return Response::json(['message' => $e->getMessage()], 500);
        }

        return Response::json(array('success' => true, 'message' => 'Usuário criado com sucesso!'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        //
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
     * @return JsonResponse
     */
    public function update()
    {
        try {
            //dd($this->request->all());
                $rules = [
                    'nome' => 'required:'.$this->user->table.'|max:255',
                    'login' => 'required|max:15|unique:' . $this->user->table . ',login,' . $this->request->input("id"),
                    'senha' => 'required:'.$this->user->table.'|max:15',
                ];

            $validator = Validator::make($this->request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return Response::json(array('success' => false, 'message' => 'Erro ao atualizar usuário:'. $errors), 400);
            }

            //OK
            $this->user = $this->user::find($this->request->input("id"));

            $this->user->nome = $this->request->input("nome");
            $this->user->login = strtolower($this->request->input("login"));
            $this->user->senha = Hash::make($this->request->input("senha"));
            $this->user->status = $this->request->input("status");

            $this->user->save();

            return Response::json(array('success' => true, 'message' => 'Usuário atualizado com sucesso!'), 200);

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return Response::json(array('success' => false, 'message' => 'Usuário já cadastrada!!'), 400);
            }
        }catch (Throwable $e) {
            return Response::json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try{
            $users = $this->user::find($id)->delete();

            if(!$users)
                return Response::json(array("success" => false, "message" => utf8_encode("Usuário não localizado para deleção com o id: [ {$id} ]")), 400);

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e ), 500);
        }
        return Response::json(array("success" => true, "message" => "Usuário(a) deletado(a) com sucesso!"),200);
    }


    public function timezone()
    {
        echo date_default_timezone_get();
    }
}
