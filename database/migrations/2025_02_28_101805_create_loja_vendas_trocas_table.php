<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaVendasTrocasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_vendas_trocas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venda_id_original');
            $table->foreign('venda_id_original')->references('id')->on('loja_vendas');

            $table->unsignedBigInteger('nova_venda_id')->nullable(true);
            $table->foreign('nova_venda_id')->references('id')->on('loja_vendas');

            $table->decimal('valor_total_troca', 10, 2);
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
        Schema::dropIfExists('loja_vendas_trocas');
    }
}
