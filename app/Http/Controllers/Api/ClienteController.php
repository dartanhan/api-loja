<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Models\ClienteModel;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{

    protected  $request, $clienteModel;

    public function __construct(Request $request, ClienteModel $clienteModel){
        $this->request = $request;
        $this->clienteModel = $clienteModel;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $modelCliente = $this->clienteModel::orderBy('nome', 'ASC')->get();

        if (!$modelCliente->isEmpty())
            //return Response::json(["clientes"=> $modelCliente], 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            return Response::json(['success' => true,'message' => "sucesso", 'clientes' => $modelCliente], 200,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        else
            return Response::json(['success' => false,'message' => "Nenhum cliente localizado! ", 'clientes' => $modelCliente], 200,[],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request = $this->request->all();

            $request['email'] = ($request['email'] === null) ? "kn@gmail.com.br"  : $request['email'];
            //$request = $request->toArray();

            $rules = [
                'email'=>'email|regex:/(.+)@(.+)\.(.+)/i'
            ];

            $validated = Validator::make($request,$rules,$messages = [
                '*'=> 'Informe um email válido!',
            ]);

            //Verifica se temos erros no form
            if ($validated->fails())
            {
                $error = $validated->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            /**
             * Cria o array com os dados
             */

            if(!$this->verifyCPF($request['cpf']))
                return Response::json(array('success' => false, "message" => "CPF Informado inválido [ ". $request['cpf'] ." ]"), 202, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $dados["id"] = $request['id'];
            $dados["telefone"] = $request['telefone'];
            $dados["cpf"] = $request['cpf'];
            $dados["nome"] = $request['nome'];
            $dados["email"] = $request['email'];
            $dados["cep"] = $request['cep'];
            $dados["logradouro"] = $request['logradouro'];
            $dados["numero"] = $request['numero'];
            $dados["complemento"] = $request['complemento'];
            $dados["bairro"] = $request['bairro'];
            $dados["localidade"] = $request['localidade'];
            $dados["uf"] = $request['uf'];
            $dados["taxa"] = $request['taxa'];

            //Cria o cliente ou atualiza
             $matchThese = array('id' => $request['id']);
            $cliente = $this->clienteModel::updateOrCreate($matchThese, $dados);

             $message = $cliente->wasRecentlyCreated == true ? "Cliente cadastrado com sucesso!" : "Cliente atualizado com sucesso!";

            DB::commit();

            if ($cliente) {
             return Response::json(array('success' => true, "message" => $message), 201, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } else {
                return Response::json(array('success' => false, "message" => 'Cliente não cadastrado!'), 400, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

        } catch (QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if($errorCode == 1062){
                    DB::rollBack();
                    return Response::json(array('success' => false, 'message' => 'Já existe um cliente cadastrado com o CPF [ ' .$dados["cpf"] .' ]'), 400);
                }
        } catch (\Throwable $e) {
            DB::rollBack();
            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'code' => 500), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClienteModel $clienteModel
     * @return JsonResponse
     */
    public function show($param)
    {
        //echo $telefone;
        $check = $this->verifyCPF($param);

        $modelCliente = $this->clienteModel::where('telefone', $param)->get();
        $tipo = "TELEFONE";
        if ($check) {
            $modelCliente = $this->clienteModel::where('cpf', $param)->get();
            $tipo = "CPF";
        }

        if (!$modelCliente->isEmpty()) {

            foreach ($modelCliente as $value) {
                $this->clienteModel = new ClienteModel();
                $this->clienteModel->id = $value['id'];
                $this->clienteModel->cpf = $value['cpf'];
                $this->clienteModel->telefone = $value['telefone'];
                $this->clienteModel->nome = $value['nome'];
                $this->clienteModel->email = $value['email'];
                $this->clienteModel->cep = $value['cep'];
                $this->clienteModel->logradouro = $value['logradouro'];
                $this->clienteModel->numero = $value['numero'];
                $this->clienteModel->complemento = $value['complemento'];
                $this->clienteModel->bairro = $value['bairro'];
                $this->clienteModel->localidade = $value['localidade'];
                $this->clienteModel->uf = $value['uf'];
                $this->clienteModel->taxa = $value['taxa'];
                $this->clienteModel->created_at = $value['created_at'];
                $this->clienteModel->updated_at = $value['updated_at'];
                $this->clienteModel->success = true;
                $this->clienteModel->message = "sucesso";
            }


            return Response::json($this->clienteModel, 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            return Response::json(array('success' => false, "message" => "Nenhum cliente localizado com o $tipo informado [ $param ]"), 400, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\\ClienteModel  $clienteModel
     * @return JsonResponse
     */
    public function edit($id)
    {
        $modelCliente = $this->clienteModel::find($id);

        if ($modelCliente) {
            return Response::json($modelCliente, 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            return Response::json(array('success' => false, "message" => "Nenhum cliente localizado com o ID: $id"), 400, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\\ClienteModel  $clienteModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClienteModel $clienteModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\\ClienteModel  $clienteModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClienteModel $clienteModel)
    {
        //
    }

    function verifyCPF( $cpf )
    {
        $cpf = "$cpf";
        if (strpos($cpf, "-") !== false)
        {
            $cpf = str_replace("-", "", $cpf);
        }
        if (strpos($cpf, ".") !== false)
        {
            $cpf = str_replace(".", "", $cpf);
        }
        $sum = 0;
        $cpf = str_split( $cpf );
        $cpftrueverifier = array();
        $cpfnumbers = array_splice( $cpf , 0, 9 );
        $cpfdefault = array(10, 9, 8, 7, 6, 5, 4, 3, 2);
        for ( $i = 0; $i <= 8; $i++ )
        {
            $sum += $cpfnumbers[$i]*$cpfdefault[$i];
        }
        $sumresult = $sum % 11;
        if ( $sumresult < 2 )
        {
            $cpftrueverifier[0] = 0;
        }
        else
        {
            $cpftrueverifier[0] = 11-$sumresult;
        }
        $sum = 0;
        $cpfdefault = array(11, 10, 9, 8, 7, 6, 5, 4, 3, 2);
        $cpfnumbers[9] = $cpftrueverifier[0];
        for ( $i = 0; $i <= 9; $i++ )
        {
            $sum += $cpfnumbers[$i]*$cpfdefault[$i];
        }
        $sumresult = $sum % 11;
        if ( $sumresult < 2 )
        {
            $cpftrueverifier[1] = 0;
        }
        else
        {
            $cpftrueverifier[1] = 11 - $sumresult;
        }
        $returner = false;
        if ( $cpf == $cpftrueverifier )
        {
            $returner = true;
        }


        $cpfver = array_merge($cpfnumbers, $cpf);

        if ( count(array_unique($cpfver)) == 1 || $cpfver == array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0) )

        {

            $returner = false;

        }
        return $returner;
    }
}
