<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaProdutosControlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_produtos_controle', function (Blueprint $table) {
            $table->id()->unsigned()->index()->autoIncrement();

            $table->bigInteger('products_variation_id')->unsigned();
            $table->foreign('products_variation_id')->references('id')->on('loja_produtos_variacao');

            $table->decimal('valor_custo', 9,2)->default('0.00');
            $table->integer('quantidade')->default(0);

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
        Schema::dropIfExists('loja_produtos_controle');
    }
}
