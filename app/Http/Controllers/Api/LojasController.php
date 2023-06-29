<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Models\FormaPagamentos;
use App\Http\Models\Lojas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class LojasController extends Controller
{
    protected  $request, $lojas;

    public function __construct(Request $request, Lojas $lojas){
        $this->request = $request;
        $this->lojas = $lojas;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = $this->request->header('store-id');
        $retorno = $this->lojas::findOrFail($id);

        return Response::json($retorno,200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd("store");


        $loja = $this->lojas::create($this->request->all());

        if ($loja->exists) {
            return Response::json(array('success' => true, 'message' => 'Loja criada com sucesso!'), 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $retorno = $this->lojas::all();

        return Response::json($retorno,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
