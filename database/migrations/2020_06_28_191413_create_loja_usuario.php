<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaUsuario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('password');
            $table->string('login')->unique();
            $table->tinyInteger('status')->default(false);
            $table->unsignedBigInteger('loja_id');
            $table->foreign('loja_id')->references('id')->on('loja_lojas');
            $table->char('sexo');
            $table->tinyInteger('admin')->default(false);
            $table->string('database');
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
        Schema::dropIfExists('loja_usuario');
    }
}
