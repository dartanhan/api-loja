<?php

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * @method static create($data)
 */
class Produto extends Model implements Auditable
{
    use AuditableTrait;

    public $table = 'loja_produtos_new';
    protected $fillable = ['codigo_produto','descricao','status','valor_produto','valor_dinheiro','valor_cartao',
        'percentual','fornecedor_id','categoria_id','cor_id','ncm','cest','origem_id','imagem','gtin'];

    function produtos() {
        return  $this->hasMany('App\Http\Models\ProdutoQuantidade');
    }

    function products() {
        return  $this->hasMany(ProdutoVariation::class,'products_id', 'id')
            ->leftJoin('loja_produtos_imagens', 'loja_produtos_variacao.id', '=', 'loja_produtos_imagens.produto_variacao_id')
            ->where('loja_produtos_variacao.status', true)
            ->select("loja_produtos_variacao.*",
                            "loja_produtos_imagens.path","loja_produtos_imagens.id as id_image",
                            "loja_produtos_imagens.produto_variacao_id",
                            (DB::raw('IF((loja_produtos_variacao.status = true), "ATIVO", "INATIVO") as status')));
    }

    function variances()
    {
        return  $this->hasMany(ProdutoVariation::class,'products_id', 'id')
            ->leftJoin('loja_produtos_imagens', 'loja_produtos_variacao.id', '=', 'loja_produtos_imagens.produto_variacao_id')
            ->where('loja_produtos_variacao.status', false)
            ->select("loja_produtos_variacao.*",
                "loja_produtos_imagens.path","loja_produtos_imagens.id as id_image",
                "loja_produtos_imagens.produto_variacao_id",
                (DB::raw('IF((loja_produtos_variacao.status = true), "ATIVO", "INATIVO") as status')));
    }

    public function listaDeCompras()
    {
        return $this->hasMany(ListaDeCompras::class, 'produto_new_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(ProdutoImagem::class, 'produto_id', 'id');
    }

    public function variacoes()
    {
        return $this->hasMany(ProdutoVariation::class, 'products_id', 'id');
    }

    public function categorias(){
        return $this->hasMany(Categoria::class, 'id', 'categoria_id');
    }
}
