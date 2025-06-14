<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimentacaoEstoque extends Model
{
    use HasFactory;

    public $table = 'loja_movimentacoes_estoque';
    protected $fillable = ['variacao_id','venda_id','tipo','quantidade','quantidade_antes','quantidade_movimentada','quantidade_depois','motivo'];

    public function variacao()
    {
        return $this->belongsTo(ProdutoVariation::class, 'variacao_id');
    }

    public function venda()
    {
        return $this->belongsTo(Vendas::class, 'venda_id');
    }
}
