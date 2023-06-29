<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class VendasPdv extends Model
{
    public $table = 'loja_vendas_pdv';
    public $timestamps = false;
    protected $fillable = ['codigo_produto','descricao','quantidade','valor_atacado','valor_varejo'];


}
