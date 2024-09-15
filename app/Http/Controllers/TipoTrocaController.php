<?php

namespace App\Http\Controllers;

use App\Http\Models\TipoTroca;
use App\Http\Models\Usuario;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TipoTrocaController extends Controller
{
    protected $request, $tipoTroca;

    public function __construct(Request $request, TipoTroca $tipoTroca){
        $this->request = $request;
        $this->tipoTroca = $tipoTroca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $user_data = Usuario::where("user_id",auth()->user()->id)->first();

        return view('admin.tipo-troca', compact('user_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        try {
            $query = $this->tipoTroca->get();

            return DataTables::of($query)->make(true);

        } catch (\Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @return JsonResponse
     */
    public function store()
    {
        try {
            $validator  = Validator::make($this->request->all(), [
                'descricao' => 'required|unique:'.$this->tipoTroca->table.'|max:50',
                'slug' => 'required|max:50',
            ],[
                'descricao.max'=> 'Descrição deve serde até 10 caracteres!',
                'descricao.required'=> 'Descrição é obrigatório!',
                'descricao.unique'  => 'Descrição já cadastrada!',
                'slug.required'=> 'Slug é obrigatório!',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            // Pegar os dados validados
            $validatedData = $validator->validated();

            // Agora você pode usar os dados validados para salvar no banco
            $tipoTroca = new TipoTroca();
            $tipoTroca->descricao = $validatedData['descricao'];
            $tipoTroca->slug = $validatedData['slug'];
            $tipoTroca->save();

            return Response::json(array('success' => true, 'message' => 'Tipo Troca criada com sucesso!'), 200);
        } catch (\Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @return JsonResponse
     */
    public function update()
    {
        try {

            $validator  = Validator::make($this->request->all(), [
                'descricao' => [
                                'required',
                                'max:50',
                                Rule::unique($this->tipoTroca->table)->ignore($this->request->input('id'))
                                ],
                'slug' => 'required|max:50',
            ],[
                'descricao.max'=> 'Descrição deve serde até 10 caracteres!',
                'descricao.required'=> 'Descrição é obrigatório!',
                'descricao.unique'  => 'Descrição já cadastrada!',
                'slug.required'=> 'Slug é obrigatório!',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            // Pegar os dados validados
            $validatedData = $validator->validated();

            $tipoTroca = $this->tipoTroca::find($this->request->input('id'));

            $tipoTroca->descricao = $validatedData['descricao'];
            $tipoTroca->slug = $validatedData['slug'];

            $tipoTroca->save();

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
            $taxes = $this->tipoTroca::find($id)->delete();

            if(!$taxes)
                return Response::json(array("success" => false, "message" => utf8_encode("Tipo Troca não localizado para deleção com o id: [ {$id} ]")), 400);

        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Tipo Troca não pode ser removida, ele está sendo usado no sistema!'), 400);
            }

        }catch (\Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e ), 500);
        }
        return Response::json(array("success" => true, "message" => "Tipo Troca deletada com sucesso!"),200);
    }
}
