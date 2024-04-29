<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaDeCompras extends Model
{
    use HasFactory;
    public $table = 'loja_lista_de_compras';
    protected $fillable = ['produto_new_id','produto_variacao_id'];

    public function getCreatedAtAttribute($value)
    {
        return date('d/m/Y H:i:s', strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d/m/Y H:i:s', strtotime($value));
    }

    public function produtoNew()
    {
        return $this->belongsTo(Produto::class, 'produto_new_id', 'id');
    }

    public function produtoVariacao()
    {
        return $this->belongsTo(ProdutoVariation::class, 'produto_variacao_id', 'id')->where('status',true); //somente ativos;
    }
}
