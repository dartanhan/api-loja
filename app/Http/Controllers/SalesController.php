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
            //pega os ids do usuario e cliente
            $usuarioId = $request->input('usuarioId');
            $clienteId = $request->input('clienteId');

            //verifico se tem venda aberta para o usuário (atendente)
            $venda = Carts::where('user_id', $usuarioId)
               // ->where('cliente_id', $clienteId)
                ->where('status',StatusVenda::ABERTO)->get();

            //caso sim, não permite retorno de venda de outro cliente.
            if(count($venda) > 0){
                return response()->json(['success' => false, 'message' => 'Para retornar a venda ao carrinho, finalize a venda que está ativa primeiro!']);
            }

            //Busca todas as vendas pendentes
            $venda = Carts::where('user_id', $usuarioId)
                ->where('cliente_id', $clienteId)
                ->where('status',StatusVenda::PENDENTE)->get();

            //extrai os ids das vendas
            $ids = $venda->pluck('id')->toArray();

            // Atualiza a tabela Carts com o novo status
            Carts::whereIn('id', $ids)->update(['status' => $request->status]);

            return response()->json(['success' => true, 'message' => 'O status da venda foi atualizado.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
