<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaProdutos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_produtos_new', function (Blueprint $table) {
           $table->unsignedBigInteger('id')->autoIncrement();
           $table->string('codigo_produto', 25)->unique();
           $table->string('descricao', 255);
           $table->boolean('status')->default(false)->comment('0 = bloqueado 1 = liberado');
           $table->decimal('valor_produto', 9,2)->default('0.00');
           $table->decimal('valor_cartao', 9,2)->default('0.00');
           $table->decimal('valor_dinheiro', 9,2)->default('0.00');
           $table->decimal('percentual', 9,2)->default('99.9');

           $table->bigInteger('fornecedor_id')->unsigned()->nullable(true);

           $table->bigInteger('categoria_id')->unsigned()->nullable(true);

           $table->bigInteger('cor_id')->unsigned()->nullable(true);

           $table->bigInteger('origem_id')->unsigned()->nullable(true);

           $table->Integer('cest')->nullable(true);
           $table->Integer('ncm')->nullable(true);
           $table->string('imagem',250)->nullable(true);

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
        Schema::dropIfExists('loja_produtos_new');
    }
}
