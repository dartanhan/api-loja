<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaLojas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_lojas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->boolean('status')->default(0);
            $table->string('razao');
            $table->string('cnpj',18);
            $table->string('endereco');
            $table->string('local');
            $table->string('local',12);
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
        Schema::dropIfExists('loja_lojas');
    }
}
