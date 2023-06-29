<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TaxaCartao extends Model
{
    public $table = 'loja_taxa_cartoes';
    protected $fillable = ['forma_id','valor_taxa'];
}
