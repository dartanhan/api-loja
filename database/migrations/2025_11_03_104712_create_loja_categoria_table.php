<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaCategoriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_categorias', function (Blueprint $table) {
            $table->bigInteger()->nullable(false)->autoIncrement();
            $table->string('nome')->nullable(false);
            $table->boolean('status')->nullable(false)->default(false);
            $table->string('slug')->nullable(false);
            $table->string('imagem')->nullable(false);
            $table->integer('quantidade')->nullable(false)->default(0);
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
        Schema::dropIfExists('loja_categoria');
    }
}
