<?php

namespace App\AI\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesIntelligenceService
{
    /**
     * Calcula métricas comerciais consolidadas para a loja atual.
     */
    public function getPerformanceComercial(string $dataInicio = null, string $dataFim = null): array
    {
        $inicio = $dataInicio ? Carbon::parse($dataInicio)->startOfDay() : Carbon::now()->startOfMonth();
        $fim = $dataFim ? Carbon::parse($dataFim)->endOfDay() : Carbon::now()->endOfDay();

        // Tabela loja_vendas
        $row = DB::table('loja_vendas')
            ->whereBetween('created_at', [$inicio, $fim])
            ->selectRaw(
                'COALESCE(SUM(total), 0) AS faturamento,'
                .'COUNT(*) AS pedidos,'
                .'COALESCE(SUM(desconto), 0) AS total_desconto'
            )
            ->first();

        $faturamento = (float) ($row->faturamento ?? 0);
        $pedidos = (int) ($row->pedidos ?? 0);
        $ticketMedio = $pedidos > 0 ? $faturamento / $pedidos : 0.0;

        return [
            'periodo' => [
                'inicio' => $inicio->toIso8601String(),
                'fim' => $fim->toIso8601String(),
            ],
            'faturamento' => round($faturamento, 2),
            'pedidos' => $pedidos,
            'ticket_medio' => round($ticketMedio, 2),
            'total_desconto' => round((float) ($row->total_desconto ?? 0), 2),
        ];
    }
}
