<?php

namespace App\Http\Controllers;

use App\Http\Models\Payments;
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

class PaymentController extends Controller
{

    protected $request,$payment;

    public function __construct(Request $request, Payments $payment){

        $this->request = $request;
        $this->payment = $payment;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $user_data = Usuario::where("user_id",auth()->user()->id)->first();
        return view('admin.payment',compact('user_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        try {
            $ret = $this->payment->get();

        } catch (Throwable $e) {
            return Response::json(['error' => $e], 500);
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
        try {
            $validator = Validator::make($this->request->all(), [
                'nome' => 'required|unique:'.$this->payment->table.'|max:155',
            ],[
                'nome.unique'  => 'Forma de Pagamento já cadastrado!',
                'nome.max'=> 'Forma de Pagamento deve ser menos que 150 caracteres!',
                'nome.required'=> 'Forma de Pagamento é obrigatório!'
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            //OK
            $this->payment::create($this->request->all());
            return Response::json(array('success' => true, 'message' => 'Dados cadastrados com sucesso!'), 201);

        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage()), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $ordination
     * @return JsonResponse
     */
    public function show($ordination)
    {
        try {

            $pay =  $this->payment::orderBy("nome",$ordination)->get();

            if(empty($pay)) {
                return Response()->json(['success' => "Nenhuma informação localizada!"], 201);
            }

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response()->json($pay,200);
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
            $validated = Validator::make($this->request->all(), [
                'nome' => 'required|max:255|unique:' . $this->payment->table . ',nome,' . $this->request->input('id')
            ],[
                'nome.unique'  => 'Forma de Pagamento já cadastrado!',
                'nome.max'=> 'Forma de Pagamento deve ser menos que 150 caracteres!',
                'nome.required'=> 'Forma de Pagamento é obrigatório!'
            ]);

            //Verifica se temos erros no form
            if ($validated->fails())
            {
              //  return redirect()->route('payment.index')->withInput()->withErrors($validated);
                return Response::json(array('success' => false, 'message' => $validated), 400);
            }

            $this->payment  = $this->payment::find($this->request->input('id'));
            $this->payment->nome = $this->request->input('nome');

            $this->payment->save();

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return Response::json(array('success' => false, 'message' => 'Forma de Pagamento já cadastrada!'), 400);
            }
        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'FormaPagamentoController -> update()' . $e ), 500);
        }
        return Response::json(array('success' => true, 'message' => 'Atualizado com sucesso!'), 200);
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
            $payments = $this->payment::find($id)->delete();

            if(!$payments)
                return Response::json(array("success" => false, "message" => utf8_encode("Forma de Pagamento localizado para deleção com o id: [ {$id} ]")), 200);

        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Forma de Pagamento não pode ser removido, ele está sendo usado no sistema!'), 400);
            }

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'PaymentController -> delete()' . $e ), 500);
        }
        return Response::json(array("success" => true, "message" => "Forma de Pagamento deletada com sucesso!"),200);
    }
}
