<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendasTroca extends Model
{
    use HasFactory;

    protected $table = 'loja_vendas_trocas';

    protected $fillable = [
        'venda_id_original',
        'nova_venda_id',
        'valor_total_troca'
    ];

    public function vendaOriginal()
    {
        return $this->belongsTo(Vendas::class, 'venda_id_original','id');
    }

    public function novaVenda()
    {
        return $this->belongsTo(Vendas::class, 'nova_venda_id','id');
    }

    public function produtosTrocados()
    {
        return $this->hasMany(VendasProdutosTroca::class, 'troca_id');
    }

}
