<?php

namespace App\Http\Controllers;

use App\Http\Models\Cashback;
use App\Http\Models\Usuario;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class CashbackController extends Controller
{
    protected $request, $cashback;


    public function __construct(Request $request, Cashback $cashback){
        $this->request = $request;
        $this->cashback = $cashback;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $user_data = Usuario::where("user_id",auth()->user()->id)->first();
        return view('admin.cashback',compact('user_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        try {
            $cashbacks = $this->cashback::get();

            if(empty($cashbacks))
                return Response()->json(array('data'=>''));


        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response()->json($cashbacks);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        try {
            $validator  = Validator::make($this->request->all(), [
                'taxa' => 'required:'.$this->cashback->table.'|max:10',
            ],[
                'taxa.max'=> 'Valor Taxa deve ser menos que 10 caracteres!',
                'taxa.required'=> 'Valor Taxa é obrigatório!',
                '//forma_id.unique'  => 'Tarifa já cadastrada!',
                //'forma_id.required'  => 'Forma ID é obrigatório!',
                //'forma_id.max'  => 'Forma ID deve ser menos que 10 caracteres!'
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            $data['taxa'] = str_replace(',', '.', str_replace('R$ ', '', $this->request->input("taxa")));
            $data['valor'] = str_replace(',', '.', str_replace('R$ ', '', $this->request->input("valor")));
           // $data['valor_final'] = str_replace(',', '.', str_replace('R$ ', '', $this->request->input("valor_final")));

            $this->cashback::create($data);

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response::json(array('success' => true, 'message' => 'Cashback criado com sucesso!'), 201);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        try {
            $this->cashback = $this->cashback::find($this->request->input('id'));

            $this->cashback->taxa = str_replace(',', '.', str_replace('%', '', $this->request->input("taxa")));
            $this->cashback->valor = str_replace(',', '.', str_replace('R$ ', '', $this->request->input("valor")));
           // $this->cashback->valor_final = str_replace(',', '.', str_replace('R$ ', '', $this->request->input("valor_final")));


            $this->cashback->save();

            return Response::json(array('success' => true, 'message' => 'Dados atualizados com sucesso!'), 200);

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'CashBackController -> update()' . $e->getMessage() ), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try{
            $cashback = $this->cashback::find($id)->delete();

            if(!$cashback)
                return Response::json(array("success" => false, "message" => utf8_encode("Cashback não localizado para deleção com o id: [ {$id} ]")), 400);

        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Cashback não pode ser removida, ele está sendo usado no sistema!'), 400);
            }

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e ), 500);
        }
        return Response::json(array("success" => true, "message" => "Cashback deletada com sucesso!"),200);
    }
}
