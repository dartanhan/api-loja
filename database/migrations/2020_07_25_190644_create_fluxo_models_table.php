<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFluxoModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_fluxo_caixa', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor_caixa');
            $table->decimal('valor_sangria');
            $table->string('descricao');
            $table->integer('loja_id');
            $table->boolean('confirme', false);
            $table->decimal('total_caixa');
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
        Schema::dropIfExists('loja_fluxo_caixa');
    }
}
