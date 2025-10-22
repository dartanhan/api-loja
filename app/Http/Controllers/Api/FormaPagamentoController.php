<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Models\FormaPagamentos;
use App\Http\Models\Payments;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FormaPagamentoController extends Controller
{
    protected  $request, $payment;

    public function __construct(Request $request, Payments $payment){
        $this->request = $request;
        $this->payment = $payment;
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $id = $this->request->header('tipo-id');
        //$database = $this->request->header('database');

        //$return = $this->forma::on($database)->findOrFail($id);
        $return = $this->payment::findOrFail($id);

        return Response::json($return,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        return FormaPagamentos::where('status', 1)->get([
            'id',
            'nome',
            'slug',
            'status'
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //
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
