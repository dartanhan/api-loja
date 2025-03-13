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

            $table->unsignedBigInteger('fornecedor_id');
            $table->foreign('fornecedor_id')->references('id')->on('loja_fornecedores');

            $table->unsignedBigInteger('categoria_id');
            $table->foreign('categoria_id')->references('id')->on('loja_categorias');

            $table->unsignedBigInteger('loja_venda_id_troca');
            $table->foreign('loja_venda_id_troca')->references('id')->on('loja_vendas');
    

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
