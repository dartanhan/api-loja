<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaVendasProdutosDescontosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public $timestamps = false;
    public function up()
    {
        Schema::create('loja_vendas_produtos_descontos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venda_id');
            $table->foreign('venda_id')->references('id')->on('loja_vendas');
            $table->integer('valor_desconto');
            $table->decimal('valor_recebido');
            $table->decimal('valor_percentual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loja_vendas_produtos_descontos');
    }
}
