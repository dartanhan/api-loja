<?php

namespace App\http\Models;

use Illuminate\Database\Eloquent\Model;

class NfceTributosModels extends Model
{
    public $table = 'loja_nfce_tributos';
    protected $fillable = ['valor_icms','percentual_icms','origem_icms','cst_icms','modbc_icms',
                            'vbc_icms','valor_pis','percentual_pis','cst_pis','vbc_pis','valor_cofins',
                            'percentual_cofins','cst_cofins','vbc_cofins','created_at'];

    /*function produtos() {
        return  $this->hasMany('App\\Http\Models\ProdutoQuantidade');
    }*/
}
