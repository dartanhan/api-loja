<?php

namespace App\Traits;

use App\Http\Models\MovimentacaoEstoque;
use Exception;
use Illuminate\Support\Carbon;
use Throwable;

trait MovimentacaoTrait
{
    /**
     * salva a movimentacaoSaida
     * @param $produto
     * @param $venda
     * @param $data
     * @throws Exception
     */
    function movimentacaoSaida($produto, $venda,$data){
        try {
            MovimentacaoEstoque::create([
                'variacao_id' => $produto["variacao_id"],
                'venda_id' => $venda->id,
                'tipo' => 'saida',
                'quantidade' => $produto["quantidade"],
                'motivo' => 'Venda finalizada',
                'quantidade_antes' => $data["antes"],
                'quantidade_movimentada' => $data["movimentada"],
                'quantidade_depois' => $data["depois"]
            ]);
        } catch (Throwable $e) {
            throw new Exception("Erro em movimentacaoSaida: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * salva a movimentacaoEntrada
     * @throws Exception
     */
    function movimentacaoEntrada($data){
        try {
            $save = array(
                'variacao_id' => $data['variacao_id'],
                'tipo' => 'entrada',
                'quantidade' =>  $data['quantidade'],
                'motivo' => 'Reposição/Atualização de estoque',
                'quantidade_antes' => $data["antes"],
                'quantidade_movimentada' => $data["movimentada"],
                'quantidade_depois' => $data["depois"]
            ) ;

            $this->movimentacaoEstoque::create($save);
        } catch (Throwable $e) {
            throw new Exception("Erro em movimentacaoEntrada: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     *  Formatar valores
     * @param $valor
     * @param $formatter
     * @return float
     */
    function formatarValor($valor, $formatter)
    {
        return $formatter->parse(str_replace(['R$', ' '], '', $valor));
    }

    /**
     *  Formatar datas
     * @param $data
     * @return |null
     */
    private function formatarData($data)
    {
        if ($data === "00/00/0000" || empty($data)) {
            return "0000-00-00";
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $data)->format('Y-m-d');
        } catch (Exception $e) {
            return null;
        }
    }
}
