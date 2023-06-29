<?php

namespace App\Http\Controllers;

use App\Http\Models\TaxaCartao;
use App\Http\Models\Usuario;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class TarifaController extends Controller
{

    protected $request, $taxa;

    public function __construct(Request $request, TaxaCartao $taxa){
        $this->request = $request;
        $this->taxa = $taxa;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $user_data = Usuario::where("user_id",auth()->user()->id)->first();

        return view('admin.tarifa', compact('user_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        try {
            $taxes = $this->taxa::
                join('loja_forma_pagamentos','loja_forma_pagamentos.id','=','loja_taxa_cartoes.forma_id')
                ->select(
                        'loja_taxa_cartoes.*',
                        'loja_forma_pagamentos.nome'
                )->get();

            foreach ($taxes as $value){
                $data['id'] =  $value->id;
                $data['forma_nome'] =  $value->nome;
                $data['valor_taxa'] =  $value->valor_taxa;
                $data['forma_id'] =  $value->forma_id;
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
     * @return JsonResponse
     */
    public function store()
    {
        try {
            $validator  = Validator::make($this->request->all(), [
                'valor_taxa' => 'required:'.$this->taxa->table.'|max:10',
                'forma_id' => 'required|unique:'.$this->taxa->table.'|max:10'
            ],[
                'valor_taxa.max'=> 'Valor Taxa deve ser menos que 10 caracteres!',
                'valor_taxa.required'=> 'Valor Taxa é obrigatório!',
                'forma_id.unique'  => 'Tarifa já cadastrada!',
                'forma_id.required'  => 'Forma ID é obrigatório!',
                'forma_id.max'  => 'Forma ID deve ser menos que 10 caracteres!'
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            $data['valor_taxa'] = str_replace(',', '.', str_replace('R$ ', '', $this->request->input("valor_taxa")));
            $data['forma_id'] = $this->request->input("forma_id");
            $data['status'] = $this->request->input("status");


            $this->taxa::create($data);

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response::json(array('success' => true, 'message' => 'Taxa criada com sucesso!'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @return JsonResponse
     */
    public function update()
    {
        try {
           $this->taxa = $this->taxa::find($this->request->input('id'));

            $this->taxa->valor_taxa = str_replace(',', '.', str_replace('R$ ', '', $this->request->input("valor_taxa")));
            $this->taxa->forma_id = $this->request->input("forma_id");

            $this->taxa->save();

            return Response::json(array('success' => true, 'message' => 'Dados atualizados com sucesso!'), 200);

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'CorController -> update()' . $e ), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        try{
            $taxes = $this->taxa::find($id)->delete();

            if(!$taxes)
                return Response::json(array("success" => false, "message" => utf8_encode("Taxa não localizado para deleção com o id: [ {$id} ]")), 400);

        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Taxa não pode ser removida, ele está sendo usado no sistema!'), 400);
            }

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e ), 500);
        }
        return Response::json(array("success" => true, "message" => "Taxa deletada com sucesso!"),200);
    }
}
