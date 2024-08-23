<?php

namespace App\Http\Controllers;

use App\Enums\StatusVenda;
use App\Http\Models\Carts;
use App\Http\Models\Usuario;
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

    /**
     * Datatable
    */
    public function table(){

        try {

            $user = Usuario::where('user_id', auth()->user()->id)->first();
            //dd($user->id);
            $query = Carts::with('clientes','usuario')
                ->where('status',StatusVenda::PENDENTE)
                ->where('user_id', $user->id)
                ->groupBy('user_id','cliente_id')
                ->orderBy('id', 'DESC');

            return DataTables::of($query)->make(true);

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     *Itens da venda
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tableItemSale(Request $request){

        try {
            $query = Carts::with('clientes','usuario','variations')
                ->where('status',StatusVenda::PENDENTE)
                ->where('user_id', $request->input('user_id'))
                ->where('cliente_id', $request->input('cliente_id'))
                ->orderBy('id', 'DESC');

            return DataTables::of($query)->make(true);

        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Atualiza os status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request){
        $request->validate([
            'id' => 'required|exists:loja_carts,id', // Valida se o ID existe na tabela de vendas
            'status' => 'required|string', // Valida o status
        ]);

        try {
            // Atualiza o status da venda
            $usuarioId = $request->input('usuarioId');
            $clienteId = $request->input('clienteId');


//            $venda = Carts::findOrFail($request->id);
//            $venda->status = $request->status;
//            $venda->save();

            // Atualiza a tabela Carts com o novo status
            Carts::where('id', $request->id)->update(['status' => $request->status]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
