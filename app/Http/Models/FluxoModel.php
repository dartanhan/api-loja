<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FluxoModel extends Model
{
    public $table = 'loja_fluxo_caixa';
    protected $fillable = ['valor_caixa','valor_sangria','descricao','loja_id','confirme','total_caixa','created_at','updated_at'];
}
