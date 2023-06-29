<?php

namespace App\Http\Controllers;


use App\Http\Models\ConferenciaMes;
use Illuminate\Http\Request;


class ConferenciaController extends Controller
{
    protected  $request, $conferenciaMes;

    public function __construct(Request $request, ConferenciaMes $conferenciaMes){
        $this->request = $request;
        $this->conferenciaMes = $conferenciaMes;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.conferencia');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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
     * @param  \App\ConferenciaMes  $conferenciaMes
     * @return \Illuminate\Http\Response
     */
    public function show(ConferenciaMes $conferenciaMes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ConferenciaMes  $conferenciaMes
     * @return \Illuminate\Http\Response
     */
    public function edit(ConferenciaMes $conferenciaMes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConferenciaMes  $conferenciaMes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConferenciaMes $conferenciaMes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ConferenciaMes  $conferenciaMes
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConferenciaMes $conferenciaMes)
    {
        //
    }
}
