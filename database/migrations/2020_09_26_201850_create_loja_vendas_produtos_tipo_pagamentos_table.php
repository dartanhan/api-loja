<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaVendasProdutosTipoPagamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public $timestamps = false;
    public function up()
    {
        Schema::create('loja_vendas_produtos_tipo_pagamentos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('venda_id');
            $table->foreign('venda_id')->references('id')->on('loja_vendas');

            $table->unsignedBigInteger('forma_pagamento_id');
            $table->foreign('forma_pagamento_id')->references('id')->on('loja_forma_pagamentos');

            $table->decimal('valor_pgto', 9,2)->default('0.00');
            $table->decimal('taxa', 9,2)->default('0.00');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('create_loja_vendas_produtos_tipo_pagamentos');
    }
}
