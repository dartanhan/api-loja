<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaProdutosVariacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_produtos_variacao', function (Blueprint $table) {
            $table->id()->unsigned()->index()->autoIncrement();

            $table->bigInteger('products_id')->unsigned();
            $table->foreign('products_id')->references('id')->on('loja_produtos');

            $table->string('subcodigo');
            $table->string('variacao', 255);
            $table->decimal('valor_varejo', 9,2)->default('0.00');
            $table->decimal('valor_atacado', 9,2)->default('0.00');
            $table->decimal('valor_atacado_5un', 9,2)->default('0.00');
            $table->decimal('valor_atacado_10un', 9,2)->default('0.00');
            $table->decimal('valor_lista', 9,2)->default('0.00');
            $table->decimal('valor_produto', 9,2)->default('0.00');
            $table->decimal('percentage', 9,2)->default('99.9');
            $table->integer('quantidade')->default(0);
            $table->integer('quantidade_minima')->default(2);
            $table->tinyInteger('status')->default(true);
            $table->date('validade')->default(null);

            $table->bigInteger('fornecedor_id')->unsigned();
            $table->foreign('fornecedor_id')->references('id')->on('loja_fornecedores');

            $table->integer('estoque',)->default(11);

            $table->decimal('valor_cartao_pix', 9,2)->default('0.00');
            $table->decimal('valor_parcelado', 9,2)->default('0.00');

            $table->string('gtin',15)->nullable(false);

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
        Schema::dropIfExists('loja_produtos_variacao');
    }
}
