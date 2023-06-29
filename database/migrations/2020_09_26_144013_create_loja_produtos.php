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
        $this->down();

        Schema::create('loja_produtos', function (Blueprint $table) {
           $table->id();
           $table->string('codigo_produto', 25)->unique();
           $table->string('descricao', 255);
           $table->boolean('status')->default(0);
           $table->decimal('valor_produto', 9,2)->default('0.00');
           $table->decimal('valor_venda', 9,2)->default('0.00');
           $table->decimal('percentual', 9,2)->default('99.9');

            $table->bigInteger('fornecedor_id')->unsigned();
            $table->foreign('fornecedor_id')->references('id')->on('loja_fornecedores');

            $table->bigInteger('categoria_id')->unsigned();
            $table->foreign('categoria_id')->references('id')->on('loja_categorias');

            $table->bigInteger('cor_id')->unsigned();
            $table->foreign('cor_id')->references('id')->on('loja_cores');

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
        Schema::dropIfExists('loja_produtos');
    }
}
