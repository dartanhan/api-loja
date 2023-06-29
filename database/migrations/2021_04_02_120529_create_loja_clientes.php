<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaClientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('telefone',10)->unique();
            $table->string('nome',500);
            $table->string('cep',8);
            $table->string('logradouro',500);
            $table->integer('numero');
            $table->string('complemento',500);
            $table->string('bairro',150);
            $table->string('localidade',150);
            $table->string('uf',2);

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
        Schema::dropIfExists('loja_clientes');
    }
}
