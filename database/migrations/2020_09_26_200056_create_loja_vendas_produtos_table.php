<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaVendasProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public $timestamps = false;
    public function up()
    {
        Schema::create('loja_vendas_produtos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venda_id');
            $table->foreign('venda_id')->references('id')->on('loja_vendas');

            $table->string('codigo_produto');
            $table->string('descricao');
            $table->decimal('valor_produto');
            $table->integer('quantidade');
            $table->boolean('troca')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendasProdutos');
    }
}
