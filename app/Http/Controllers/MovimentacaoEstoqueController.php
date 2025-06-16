<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Models\MovimentacaoEstoque;
use Illuminate\Support\Carbon;

class MovimentacaoEstoqueController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->only(['tipo', 'codigo']);

        $movimentacoes = MovimentacaoEstoque::with(['variacao.produtoPai','venda'])
            ->when($request->tipo, function ($query) use ($request) {
                $query->where('tipo', $request->tipo);
            })
            ->when($request->codigo, function ($query) use ($request) {
                $query->whereHas('variacao', function ($q) use ($request) {
                    $q->where('subcodigo', 'like', '%' . $request->codigo . '%');
                });
            })
            ->when($request->filled('data_range'), function ($query) use ($request) {
                [$inicio, $fim] = explode(' - ', $request->data_range);
                $query->whereBetween('created_at', [
                    Carbon::createFromFormat('d/m/Y', trim($inicio))->startOfDay(),
                    Carbon::createFromFormat('d/m/Y', trim($fim))->endOfDay()
                ]);
            }, function ($query) {
                // Caso não tenha passado a data, filtra pelo dia atual
                $query->whereDate('created_at', Carbon::today());
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.monitoramento.index', compact('movimentacoes', 'filtros'));
    }

    public function historicoProduto(Request $request)
    {
        $subcodigo = $request->input('subcodigo');
        $tipo = $request->input('tipo');
        $dataRange = $request->input('data_range');

        $historico = MovimentacaoEstoque::with(['variacao.produtoPai', 'venda'])
            ->whereHas('variacao', function ($q) use ($subcodigo) {
                $q->where('subcodigo', $subcodigo);
            })
            ->when($tipo, function ($query) use ($tipo) {
                $query->where('tipo', $tipo);
            })
            ->when($request->filled('data_range'), function ($query) use ($request) {
                [$inicio, $fim] = explode(' - ', $request->data_range);
                $query->whereBetween('created_at', [
                    Carbon::createFromFormat('d/m/Y', trim($inicio))->startOfDay(),
                    Carbon::createFromFormat('d/m/Y', trim($fim))->endOfDay()
                ]);
            })
            ->orderByDesc('created_at')
            ->get();

        return response()->json($historico);
    }

}
