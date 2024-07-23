<?php

namespace App\Http\Controllers;

use App\Http\Models\FormaPagamentos;
use App\Http\Models\Payments;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class FormaPagamentoController extends Controller
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
        $payments = $this->payment->orderBy('nome', 'ASC')->get();

        return view('admin.payments', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {

        try {
            foreach ($this->payment::all() as $value){
                $data['id'] =  $value->id;
                $data['nome'] =  $value->nome;
                $data['slug'] =  $value->slug;
                $data['status'] =  $value->status;
                $data['created_at'] =  date('d/m/Y H:i:s', strtotime($value->created_at));
                $data['updated_at'] =  date('d/m/Y H:i:s', strtotime($value->updated_at));

                $exitArray[] = $data;
            }

            if(!empty($exitArray)) {
                return Response()->json($exitArray);
            }  else {
                return Response()->json(array('data'=>''));
            }

        } catch (Throwable $e) {
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
        $validator  = Validator::make($this->request->all(), [
            'nome' => 'required|unique:'.$this->payment->table.'|max:155',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            // return \response()->json($errors, 400);
            //return Response::json($errors,400);
            return Response::json(array(
                'success' => false,
                'status' => 400,
                'message' => 'Forma de Pagamento existente na base.',
                'errors' => $errors), 400);
        }

        //OK

        try {
            $this->payment::create($this->request->all());
            return Response::json(array('success' => true, 'message' => 'Dados cadastrados com sucesso!'), 201);

        } catch (\Exception $e) {
            return Response::json(['error' => $e], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {

            $saida =  $this->payment::all();

            if(!empty($saida)) {
                return Response()->json($saida);
            }

        } catch (Throwable $e) {
            return Response::json(['error' => $e], 500);
        }
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
        //dd($request->all());
        try {
            $validator = Validator::make($this->request->all(), [
                'nome' => 'required|max:155|unique:' . $this->payment->table . ',nome,' . $this->request->id
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                return Response::json(array('success' => false, 'message' => 'Informe a Forma de Pagamento !'), 400);
            }

            $this->payment  = $this->payment::find($this->request->id);

            $this->payment->nome = $this->request->nome;
            $this->payment->slug = $this->request->slug;
            $this->formaPagamento->status = $this->request->status;

            $this->payment->save();

            return Response::json(array('success' => true, 'message' => 'Atualizado com sucesso!'), 200);
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return Response::json(array('success' => false, 'message' => 'Forma de Pagamento já cadastrada!'), 400);
            }
        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'FormaPagamentoController -> update()' . $e ), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }
}
