<?php

namespace App\Http\Controllers;

use App\Http\Models\Cor;
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

class CorController extends Controller
{

    protected $request,$cor;

    public function __construct(Request $request, Cor $cor){

        $this->request = $request;
        $this->cor = $cor;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $user_data = Usuario::where("user_id",auth()->user()->id)->first();
        return view('admin.cor',compact('user_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        try {

            foreach ($this->cor::all() as $value){
                $data['id'] =  $value->id;
                $data['nome'] =  $value->nome;
                $data['status'] =  $value->status == 1 ? "ATIVO" : "INATIVO";
                $data['created_at'] =  date('d/m/Y H:i:s', strtotime($value->created_at));
                $data['updated_at'] =  date('d/m/Y H:i:s', strtotime($value->updated_at));

                $ret[] = $data;
            }

            if(!empty($ret)) {
                return Response()->json($ret,200);
            }  else {
                return Response()->json(array('data'=>''),200);
            }

        } catch (Throwable $e) {
            return Response::json(['error' => $e], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

        $validator  = Validator::make($this->request->all(), [
            'nome' => 'required|unique:'.$this->cor->table.'|max:50',
            'status' => 'required|max:1'
        ],[
            'nome.unique'  => 'Cor já cadastrado!',
            'nome.max'=> 'Cor deve ser menos que 50 caracteres!',
            'nome.required'=> 'Cor é obrigatório!',
            'status.unique'  => 'Status é obrigatório!',
            'status.max'  => 'Cor deve ser menos que 1 caracteres!'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return Response::json(array('success' => false,'message' => $error), 400);
        }

        //OK
            $this->cor::create($this->request->all());
            return Response::json(array('success' => true, 'message' => 'Cor cadastrada com sucesso!'), 201);

        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'Error!' . $e), 500);
        }
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
     * @param int $id
     * @return JsonResponse
     */
    public function update($id)
    {
        try {

            $validator = Validator::make($this->request->all(), [
                'nome' => 'required|max:150|unique:' . $this->cor->table . ',nome,' . $this->request->input('id'),
                'status' => 'max:1'
            ],[
                'nome.unique'  => 'Cor já cadastrada!',
                'nome.max'=> 'Cor deve ser menos que 150 caracteres!',
                'nome.required'=> 'Cor é obrigatório!',
                'status.max'=> 'Status deve ser 1 caracter!',
                'status.required'=> 'Status é obrigatório!'
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            //OK
            $this->cor = $this->cor::find($this->request->input('id'));

            $this->cor->nome = $this->request->input('nome');
            $this->cor->status = $this->request->input('status');

            $this->cor->save();

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return Response::json(array('success' => false, 'message' => 'Cor já cadastrada!!'), 400);
            }
        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage() ), 500);
        }
        return Response::json(array('success' => true, 'message' => 'Cor atualizada com sucesso!'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try{
            $cors  = $this->cor::find($id)->delete();

            if(!$cors)
                return Response::json(array("success" => false, "message" => utf8_encode("Cor não localizado para deleção com o id: [ {$id} ]")), 400);

        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Cor não pode ser removido, ele está sendo usado no sistema!'), 400);
            }

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'CorController -> delete()' . $e ), 500);
        }
        return Response::json(array("success" => true, "message" => "Cor deletada com sucesso!"),200);
    }
}
