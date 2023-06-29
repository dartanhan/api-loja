<?php

namespace App\Http\Controllers;

use App\Http\Models\Categoria;
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

class CategoriaController extends Controller
{

    protected $request,$categoria;

    public function __construct(Request $request, Categoria $categoria){

        $this->request = $request;
        $this->categoria = $categoria;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $user_data = Usuario::where("user_id",auth()->user()->id)->first();
        return view('admin.categoria',compact('user_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        try {

            foreach ($this->categoria::all() as $value){
                $data['id'] =  $value->id;
                $data['nome'] =  $value->nome;
                $data['quantidade'] =  $value->quantidade;
                $data['status'] =  $value->status == 1 ? "ATIVO" : "INATIVO";
                $data['created_at'] =  date('d/m/Y H:i:s', strtotime($value->created_at));
                $data['updated_at'] =  date('d/m/Y H:i:s', strtotime($value->updated_at));

                $exit[] = $data;
            }

            if(!empty($exit)) {
                return Response()->json($exit);
            }  else {
                return Response()->json(array('data'=>''));
            }

        } catch (Throwable  $e) {
            return Response::json(['error' => $e], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store()
    {
        try{
            $validator  = Validator::make($this->request->all(),[
                'nome' => 'required|unique:'.$this->categoria->table.'|max:155',
                'status' => 'required|max:1',
                'quantidade' => 'required|max:5'
            ],[
                'nome.unique'  => 'Categoria já cadastrado!',
                'nome.required'=> 'Categoria é obrigatório!',
                'nome.max'=> 'Categoria deve ser menos que 155 caracteres!',
                'status.required'  => 'Status é obrigatório!',
                'status.max'  => 'Status deve ser 1 caracter!',
                'quantidade.required'=> 'Quantidade é obrigatório!',
                'quantidade.max'=> 'Quantidade deve ser menos que 5 caracteres!',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            //OK
            $this->categoria::create($this->request->all());


        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage() ), 500);
        }
        return Response::json(array('success' => true, 'message' => 'Categoria cadastrada com sucesso!!'), 201);
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
     *
     * @return JsonResponse
     */
    public function update()
    {
        try {

            $validator = Validator::make($this->request->all(), [
                'nome' => 'required|max:155|unique:' . $this->categoria->table . ',nome,' . $this->request->input('id'),
                'quantidade' => 'required|max:5',
                'status' => 'required|max:1'
            ],[
                'nome.unique'  => 'Categoria já cadastrado!',
                'nome.required'=> 'Categoria é obrigatório!',
                'nome.max'=> 'Categoria deve ser menos que 155 caracteres!',
                'status.required'  => 'Status é obrigatório!',
                'status.max'  => 'Status deve ser 1 caracter!',
                'quantidade.required'=> 'Quantidade é obrigatório!',
                'quantidade.max'=> 'Quantidade deve ser menos que 5 caracteres!',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            //OK
            $this->categoria = $this->categoria::find($this->request->input('id'));

            $this->categoria->nome = $this->request->input('nome');
            $this->categoria->quantidade = $this->request->input('quantidade');
            $this->categoria->status = $this->request->input('status');

            $this->categoria->save();

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return Response::json(array('success' => false, 'message' => 'Categoria já cadastrada!!'), 400);
            }
        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'CategoriaController -> update()' . $e ), 500);
        }
        return Response::json(array('success' => true, 'message' => 'Categoria atualizada com sucesso!'), 200);
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
            $category = $this->categoria::find($id)->delete();

            if(!$category)
                return Response::json(array("success" => false, "message" => utf8_encode("Categoria não localizado para deleção com o id: [ {$id} ]")), 200);

        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Categoria não pode ser removido, ele está sendo usado no sistema!'), 400);
            }

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'CategoriaController -> delete()' . $e ), 500);
        }
        return Response::json(array("success" => true, "message" => "Categoria deletada com sucesso!"),200);
    }
}
