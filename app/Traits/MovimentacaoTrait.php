<?php

namespace App\Traits;

use Illuminate\Support\Facades\Response;

trait MovimentacaoTrait
{

    function movimentacaoSaida($produto, $venda){
        $this->movimentacaoEstoque::create([
            'variacao_id' => $produto["variacao_id"],
            'venda_id' => $venda->id,
            'tipo' => 'saida',
            'quantidade' => $produto["quantidade"],
            'motivo' => 'Venda finalizada'
        ]);
    }

    function movimentacaoEntrada($data){
        $save = array(
            'variacao_id' => $data['id'],
            'tipo' => 'entrada',
            'quantidade' =>  $data['quantidade'],
            'motivo' => 'Reposição de estoque',
        ) ;

        $this->movimentacaoEstoque::create($save);
    }

}
