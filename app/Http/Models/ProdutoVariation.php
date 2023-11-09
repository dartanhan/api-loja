<?php

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @method static create($data)
 */
class ProdutoVariation extends Model
{
    protected $table = 'loja_produtos_variacao';
    protected $fillable = ['id','products_id','subcodigo','variacao','valor_varejo','valor_atacado','valor_atacado_5un','valor_atacado_10un','valor_lista','valor_produto'
                            ,'percentage','quantidade','quantidade_minima','status','validade','created_at','fornecedor','estoque','valor_cartao_pix','valor_parcelado'];

    public function variations() {
        return $this->belongsTo(ProdutoVariation::class);
    }

    function images(){
        return  $this->hasMany(ProdutoImagem::class ,'produto_variacao_id','id');
    }
}
