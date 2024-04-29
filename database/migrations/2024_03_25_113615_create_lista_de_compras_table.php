<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListaDeComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_lista_de_compras', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->unsignedBigInteger('produto_new_id'); // Chave estrangeira para o pai
            $table->foreign('produto_new_id')->references('id')->on('loja_produtos_new');

            $table->unsignedBigInteger('produto_variacao_id'); // Chave estrangeira para o filho
            $table->foreign('produto_variacao_id')->references('id')->on('loja_produtos_variacao');

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
        Schema::dropIfExists('lista_de_compras');
    }
}
