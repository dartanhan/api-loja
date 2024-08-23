<?php

namespace App\Http\Controllers;

use App\Enums\StatusVenda;
use App\Http\Models\Carts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class SalesController extends Controller
{

    public function index()
    {
        if(Auth::check() === true){
            return view('admin.sale');
        }
        return redirect()->route('admin.login');
    }

    public function table(){

        try {
            $query = Carts::with('clientes','usuario')
                ->where('status',StatusVenda::PENDENTE)
                ->orderBy('id', 'DESC');

            return DataTables::of($query)->make(true);

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }
}
