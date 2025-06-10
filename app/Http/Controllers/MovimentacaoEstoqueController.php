<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Models\MovimentacaoEstoque;

class MovimentacaoEstoqueController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->only(['tipo', 'codigo']);

        $movimentacoes = MovimentacaoEstoque::with(['variacao.produto'])
            ->when($request->tipo, function ($query) use ($request) {
                $query->where('tipo', $request->tipo);
            })
            ->when($request->codigo, function ($query) use ($request) {
                $query->whereHas('variacao', function ($q) use ($request) {
                    $q->where('subcodigo', 'like', '%' . $request->codigo . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.monitoramento.index', compact('movimentacoes', 'filtros'));
    }
}
