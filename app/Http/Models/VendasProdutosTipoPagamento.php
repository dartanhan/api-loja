<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class VendasProdutosTipoPagamento extends Model
{
    public $table = 'loja_vendas_produtos_tipo_pagamentos';
    public $timestamps = false;
    protected $fillable = ['venda_id','forma_pagamento_id','valor_pgto','taxa'];

    public function PaymentsList()
    {
        return $this->hasMany(Payments::class,'id','forma_pagamento_id');
    }
}
