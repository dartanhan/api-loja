<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaVendasProdutosValorDuplasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public $timestamps = false;
    public function up()
    {
        $this->down();
        Schema::create('loja_vendas_produtos_valor_duplas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venda_id');
            $table->foreign('venda_id')->references('id')->on('loja_vendas');
            $table->decimal('valor_cartao');
            $table->decimal('valor_dinheiro');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loja_vendas_produtos_valor_duplas');
    }
}
