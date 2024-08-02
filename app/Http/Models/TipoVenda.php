<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static orderBy(string $string, string $string1)
 * @method static create(array $all)
 * @method static find($input)
 */
class TipoVenda extends Model
{
    public $table = 'loja_tipo_vendas';
    protected $fillable = ['descricao','slug'];

    public function vendas()
    {
        return $this->hasMany( Vendas::class,'tipo_venda_id');
    }
}
