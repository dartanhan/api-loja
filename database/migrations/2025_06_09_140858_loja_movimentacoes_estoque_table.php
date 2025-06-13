<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LojaMovimentacoesEstoqueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_movimentacoes_estoque', function (Blueprint $table) {
            $table->id();

            // Correção do nome da tabela de variações
            $table->unsignedBigInteger('variacao_id')->nullable();
            $table->foreign('variacao_id')
                  ->references('id')->on('loja_produtos_variacao')->onDelete('cascade');

            $table->unsignedBigInteger('venda_id')->nullable();
            $table->foreign('venda_id')
                  ->references('id')->on('loja_vendas')->onDelete('set null');

            $table->enum('tipo', ['saida', 'entrada']);
            $table->integer('quantidade');
            $table->string('motivo')->nullable();
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
        Schema::dropIfExists('loja_movimentacoes_estoque');
    }
}
