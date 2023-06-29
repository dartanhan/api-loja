<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaVendas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_vendas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_venda')->unique();
            $table->unsignedBigInteger('loja_id');
            $table->decimal('valor_total');
            $table->foreign('loja_id')->references('id')->on('loja_lojas');
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
        Schema::dropIfExists('vendas');
    }
}
