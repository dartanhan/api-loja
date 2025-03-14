<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaVendasProdutosTrocasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_vendas_produtos_trocas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('troca_id');
            // Foreign Keys
            $table->foreign('troca_id')->references('id')->on('loja_vendas_trocas');

            $table->unsignedBigInteger('produto_id');
            // Foreign Keys
            $table->foreign('produto_id')->references('id')->on('loja_vendas_produtos');

            $table->string('codigo_produto');
            $table->string('descricao');
            $table->decimal('valor_produto', 10, 2);
            $table->integer('quantidade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loja_vendas_produtos_trocas');
    }
}
