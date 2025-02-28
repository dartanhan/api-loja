<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendasProdutosTroca extends Model
{
    use HasFactory;

    protected $table = 'loja_vendas_produtos_trocas';

    protected $fillable = [
        'troca_id',
        'produto_id',
        'codigo_produto',
        'descricao',
        'valor_produto',
        'quantidade'
    ];

    public function troca()
    {
        return $this->belongsTo(VendasTroca::class, 'troca_id');
    }
}
