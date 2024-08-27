<?php

namespace App\Http\Controllers;

use App\Http\Models\FormaEntrega;
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
use Yajra\DataTables\Facades\DataTables;

class FormaEntregaController extends Controller
{

    protected $request,$forma;

    public function __construct(Request $request, FormaEntrega $forma){

        $this->request = $request;
        $this->forma = $forma;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $formas = $this->forma->orderBy('nome', 'ASC')->get();

        return view('admin.forma', compact('formas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {

        try {
            $query = $this->forma::where('status', true);

            return DataTables::of($query)->make(true);

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
            'nome' => 'required|unique:'.$this->forma->table.'|max:155',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            // return \response()->json($errors, 400);
            //return Response::json($errors,400);
            return Response::json(array(
                'success' => false,
                'status' => 400,
                'message' => 'Forma de Entrega existente na base.',
                'errors' => $errors), 400);
        }

        //OK

        try {
            $this->forma::create($this->request->all());
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

            $saida =  $this->forma::all();

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
                'nome' => 'required|max:155|unique:' . $this->forma->table . ',nome,' . $this->request->id
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                return Response::json(array('success' => false, 'message' => 'Informe a Forma de Entrega !'), 400);
            }

            $this->forma  = $this->forma::find($this->request->id);

            $this->forma->nome = $this->request->nome;
            $this->forma->slug = $this->request->slug;
            $this->forma->status = $this->request->status;

            $this->forma->save();

            return Response::json(array('success' => true, 'message' => 'Atualizado com sucesso!'), 200);
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return Response::json(array('success' => false, 'message' => 'Forma de Entrega já cadastrada!'), 400);
            }
        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'FormaEntregaController -> update()' . $e ), 500);
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
