<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaContaPagarReceber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_gastos', function (Blueprint $table) {
            $table->id();
            $table->decimal("valor_gasto_fixo",8,2);
            $table->decimal("valor_gasto_variaval",8,2);
            $table->integer("tipo_conta");
            $table->string("observacao");
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
        Schema::dropIfExists('loja_conta_pagar_receber');
    }
}
