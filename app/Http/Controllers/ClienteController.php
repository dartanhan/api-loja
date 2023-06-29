<?php

namespace App\Http\Controllers;


use App\Http\Models\ClienteModel;
use App\Http\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ClienteController extends Controller
{

    public function __construct(Request $request, ClienteModel $cliente){

        $this->request = $request;
        $this->cliente = $cliente;
        //$this->database = $this->request->header('database');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_data = Usuario::where("user_id",auth()->user()->id)->first();
        return view('admin.cliente',compact('user_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {

            //foreach ($this->categoria::on($this->database)->all() as $value){
            foreach ($this->cliente::all() as $value){
                $retorno['id'] =  $value->id;
                $retorno['nome'] =  $value->nome;
                $retorno['telefone'] =  $value->telefone;
                $retorno['cep'] =  $value->cep;
                $retorno['logradouro'] =  $value->logradouro;
                $retorno['numero'] =  $value->numero;
                $retorno['complemento'] =  $value->complemento;
                $retorno['bairro'] =  $value->bairro;
                $retorno['localidade'] =  $value->localidade;
                $retorno['uf'] =  $value->uf;
                $retorno['created_at'] =  date('d/m/Y H:i:s', strtotime($value->created_at));
                //$retorno['updated_at'] =  date('d/m/Y H:i:s', strtotime($value->updated_at));

                $saida[] = $retorno;
            }

            if(!empty($saida)) {
                return \response()->json($saida);
            }  else {
                return \response()->json(array('data'=>''));
            }

        } catch (\Exception $e) {
            return Response::json(['error' => $e], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\\ClienteModel  $clienteModel
     * @return \Illuminate\Http\Response
     */
    public function show(ClienteModel $clienteModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\\ClienteModel  $clienteModel
     * @return \Illuminate\Http\Response
     */
    public function edit(ClienteModel $clienteModel)
    {
        //
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
}
