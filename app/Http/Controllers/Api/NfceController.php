<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Models\NfceTributosModels;
use Illuminate\Support\Facades\Response;

class NfceController extends Controller
{
    protected  $request, $nfceTributos;

    public function __construct(Request $request, NfceTributosModels $nfceTributos){
        $this->request = $request;
        $this->nfceTributos = $nfceTributos;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {

            $return = $this->nfceTributos::all();
          // dd($return);

            if (!$return->isEmpty())
                return Response::json($return, 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            else
                return Response::json(array('success' => false, "message" => "Nenhum tributo cadastrado!"), 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


        } catch (\Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage(), 'code' => 500), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
