<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProdutoIdToLojaVendasProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loja_vendas_produtos', function (Blueprint $table) {

            $table->unsignedBigInteger('produto_id')
                ->nullable()
                ->after('variacao_id');

            $table->foreign('produto_id')
                ->references('id')
                ->on('loja_produtos_new');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loja_vendas_produtos', function (Blueprint $table) {

            $table->dropForeign(['produto_id']);
            $table->dropColumn('produto_id');
        });
    }
}
