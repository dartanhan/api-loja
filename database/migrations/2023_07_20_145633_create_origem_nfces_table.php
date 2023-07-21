<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrigemNfcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_produto_origem_nfces', function (Blueprint $table) {
            $table->id()->unsigned()->index()->autoIncrement();
            $table->integer('codigo');
            $table->string('descricao', 255);
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
        Schema::dropIfExists('origem_nfces');
    }
}
