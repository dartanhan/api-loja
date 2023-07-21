<?php

namespace App\Http\Controllers;

use App\Http\Models\OrigemNfce;
use App\Http\Models\Usuario;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

use Throwable;

class OrigemNfceController extends Controller
{

    protected $request,$origem_nfce;

    public function __construct(Request $request, OrigemNfce  $origem_nfce ){
        $this->request = $request;
        $this->origem_nfce =  $origem_nfce;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if(Auth::check() === true){
            $origem_nfce = $this->origem_nfce->orderBy('codigo', 'ASC')->get();

            $user_data = Usuario::where("user_id",auth()->user()->id)->first();

            return view('admin.origem', compact('origem_nfce','user_data'));
        }

        return redirect()->route('admin.login');

    }

    /**
     * Show the form for creating a new resource.
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        try {
            $origem_nfces = $this->origem_nfce::get();

            if(empty($origem_nfces))
                return Response()->json(array('data'=>''));


        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response()->json($origem_nfces);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $this->request->all();
            $validator  = Validator::make($data, [
                'codigo' => 'required|unique:'.$this->origem_nfce->table.'|max:10',
            ],[
                'codigo.max'=> 'Valor codigo deve ser menos que 10 caracteres!',
                'codigo.required'=> 'Valor codigo é obrigatório!',
                'codigo.unique'  => 'Codigo já cadastrado!',
                //'forma_id.required'  => 'Forma ID é obrigatório!',
                //'forma_id.max'  => 'Forma ID deve ser menos que 10 caracteres!'
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            $this->origem_nfce->create($data);
            
        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage()), 500);
        }
        return Response::json(array('success' => true, 'message' => 'Origem criado com sucesso!'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $this->origem_nfce = $this->origem_nfce::find($this->request->input('id'));

            $this->origem_nfce->codigo =  $this->request->input("codigo");
            $this->origem_nfce->descricao = $this->request->input("descricao");

            $this->origem_nfce->save();

            return Response::json(array('success' => true, 'message' => 'Dados atualizados com sucesso!'), 200);

        }catch (Throwable $e) {
            return Response::json(['success' => false, 'message' => $e->getMessage() ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try{
            $origem_nfce = $this->origem_nfce::find($id)->delete();

            if(!$origem_nfce)
                return Response::json(array("success" => false, "message" => utf8_encode("Origem não localizado para deleção com o id: [ {$id} ]")), 400);

        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Origem não pode ser removida, ele está sendo usado no sistema!'), 400);
            }

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e ), 500);
        }
        return Response::json(array("success" => true, "message" => "Origem deletada com sucesso!"),200);
    }
    
}
