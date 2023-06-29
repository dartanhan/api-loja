<?php

namespace App\Http\Controllers;

use App\Http\Models\Fornecedor;

use App\Http\Models\ProdutoVariation;
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

class FornecedorController extends Controller
{

    protected $request,$supplier,$variacaoProdutos;

    public function __construct(Request $request, Fornecedor $supplier, ProdutoVariation $variacaoProdutos){

        $this->request = $request;
        $this->supplier = $supplier;
        $this->variacaoProdutos = $variacaoProdutos;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {

        $suppliers = $this->supplier->where('status',1)->orderBy('nome', 'ASC')->get();

        $user_data = Usuario::where("user_id",auth()->user()->id)->first();

        return view('admin.fornecedor', compact('suppliers','user_data'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
       /* try {

            foreach ($this->supplier::all()->sortBy('nome')->where('status',1) as $value){
                $data['id'] =  $value->id;
                $data['nome'] =  strtoupper($value->nome);
                $data['status'] =  $value->status == 1 ? "ATIVO" : "INATIVO";
                $data['created_at'] =  date('d/m/Y H:i:s', strtotime($value->created_at));
                $data['updated_at'] =  date('d/m/Y H:i:s', strtotime($value->updated_at));

                $ret[] = $data;
            }

            if(!empty($ret)) {
                return Response()->json($ret);
            }  else {
                return Response()->json(array('data'=>''));
            }

        } catch (Throwable $e) {
            return Response::json(['error' => $e], 500);
        }*/
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store()
    {
        try {
            $validated = Validator::make($this->request->all(), [
                'nome' => 'required|unique:'.$this->supplier->table.'|max:155',
                'status' => 'max:1'
            ],[
                'nome.unique'  => 'Fornecedor já cadastrado!',
                'nome.max'=> 'Fornecedor deve ser menos que 150 caracteres!',
                'nome.required'=> 'Fornecedor é obrigatório!'
            ]);

            if ($validated->fails()) {
                $errors = $validated->errors();
                return Response::json(array('success' => false,'message' => $errors), 400);
            }

            //OK

            $this->supplier::create($this->request->all());
            return Response::json(array('success' => true, 'message' => 'Fornecedor criado com sucesso!'), 201);

        } catch (Throwable $e) {
            return Response::json(['error' => $e], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $status
     * @return JsonResponse
     */
    public function show(int $status)
    {

        try {
            $sql =  $status <> 3 ? $this->supplier::all()->sortBy('nome')->where('status',$status) : $this->supplier::all()->sortBy('nome');
            foreach ($sql as $value){
                $data['id'] =  $value->id;
                $data['nome'] =  strtoupper($value->nome);
                $data['status'] =  $value->status == 1 ? "ATIVO" : "INATIVO";
                $data['created_at'] =  date('d/m/Y H:i:s', strtotime($value->created_at));
                $data['updated_at'] =  date('d/m/Y H:i:s', strtotime($value->updated_at));

                $ret[] = $data;
            }

            if(!empty($ret)) {
                return Response()->json($ret);
            }  else {
                return Response()->json(array('data'=>''));
            }

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
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
     *
     * @return JsonResponse
     */
    public function update()
    {
      // dd($this->request->all());
        try {
            $validator = Validator::make($this->request->all(), [
                'nome' => 'required|max:155|unique:' . $this->supplier->table . ',nome,' . $this->request->input('id'),
                'status' => 'required|max:1'
            ],[
                'nome.max'=> 'Fornecedor deve ser menos que 155 caracteres!',
                'nome.required'=> 'Fornecedor é obrigatório!',
                'nome.unique'  => 'Fornecedor já cadastrado!',
                'status.required'  => 'Status é obrigatório!',
                'status.max'  => 'Status deve ser menos que 1 caracteres!'
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            $status = $this->request->input('status');
            $fornecedor_id = $this->request->input('id');

            //Inativo
            if(intval($status) === 0){
                //Verifica se tem fornecedor vinculado ao produto, se tiver efetua a troca caso seja ação de inativação

                $fornecedor_new = $this->request->input('fornecedor_new');
                $supplier = $this->variacaoProdutos->where('fornecedor',$fornecedor_id)->get();

                if( $supplier){
                    foreach ($supplier as $valor) {
                        $this->variacaoProdutos->where(['id' => $valor->id])->update(['fornecedor' => $fornecedor_new]);
                    }
                }
            }

            $this->supplier = $this->supplier::find($fornecedor_id);

            $this->supplier->nome = $this->request->input('nome');
            $this->supplier->status = $this->request->input('status');

            $this->supplier->save();

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return Response::json(array('success' => false, 'message' => 'Fornecedor já cadastrado!'), 400);
            }
        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'FornecedorController -> update()' . $e ), 500);
        }
        return Response::json(array('success' => true, 'message' => 'Fornecedor atualizado com sucesso!'), 200);
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
             $suppliers = $this->supplier::find($id)->delete();

             if(!$suppliers)
                return Response::json(array("success" => false, "message" => utf8_encode("Fornecedor não localizado para deleção com o id: [ {$id} ]")), 400);

        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Fornecedor não pode ser removido, ele está sendo usado no sistema!'), 400);
            }

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'FornecedorController -> update()' . $e ), 500);
        }
        return Response::json(array("success" => true, "message" => "Fornecedor deletado com sucesso!"),200);
    }
}
