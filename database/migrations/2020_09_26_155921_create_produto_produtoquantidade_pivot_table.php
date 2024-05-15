<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutoProdutoquantidadePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public $timestamps = false;

    public function up()
    {
        Schema::create('loja_produtos_quantidade', function (Blueprint $table) {
            $table->bigInteger('produto_id')->unsigned()->index();
            $table->foreign('produto_id')->references('id')->on('loja_produtos')->onDelete('cascade');

            $table->bigInteger('cor_id')->unsigned()->index();
            $table->foreign('cor_id')->references('id')->on('loja_cores')->onDelete('cascade');

            $table->bigInteger('loja_id')->unsigned()->index();
            $table->foreign('loja_id')->references('id')->on('loja_lojas')->onDelete('cascade');

            $table->integer('quantidade')->default(0);
            $table->integer('quantidade_minima')->default(2);
            $table->tinyInteger('status')->default(1);

            $table->primary(['produto_id', 'loja_id','cor_id']);

            //$table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produto_produtoquantidade');
    }
}
