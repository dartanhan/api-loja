<?php

namespace App\Http\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Models\Audit;

/**
 * @method static create($data)
 * @method static findOrFail($variacaoId)
 */
class ProdutoVariation extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'loja_produtos_variacao';
    protected $fillable = ['id','products_id','subcodigo','variacao','valor_varejo','valor_atacado','valor_atacado_5un','valor_atacado_10un','valor_lista','valor_produto'
                            ,'percentage','quantidade','quantidade_minima','status','validade','created_at','fornecedor','estoque','descontos','gtin'];

    public function variations() {
        return $this->belongsTo(ProdutoVariation::class);
    }

    function images(){
        return  $this->hasMany(ProdutoImagem::class ,'produto_variacao_id','id');
    }

    public function venda()
    {
        return $this->belongsTo(VendasProdutos::class, 'subcodigo', 'codigo_produto');
    }

    public function listaDeCompras()
    {
        return $this->hasMany(ListaDeCompras::class, 'produto_variacao_id', 'id')->where('status',true); //somente ativos;
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor', 'id');
    }

    public function produtoPai()
    {
        return $this->belongsTo(Produto::class, 'products_id', 'id');
    }

    // No modelo ProdutoVariation (ou Produto)
    public function vendas()
    {
        return $this->hasMany(VendasProdutos::class, 'codigo_produto', 'subcodigo')
            ->whereMonth('created_at', Carbon::now()->month); // Filtrar vendas do mês
    }

    public function setValorVarejoAttribute($value)
    {
        $this->attributes['valor_varejo'] = str_replace(',', '.', $value);
    }

    public function setValorAtacadoAttribute($value)
    {
        $this->attributes['valor_atacado'] = str_replace(',', '.', $value);
    }

    public function setValorProdutoAttribute($value)
    {
        $this->attributes['valor_produto'] = str_replace(',', '.', $value);
    }

}
