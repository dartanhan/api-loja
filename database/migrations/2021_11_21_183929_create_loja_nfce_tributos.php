<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaNfceTributos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_nfce_tributos', function (Blueprint $table) {
            $table->id();

            $table->decimal("valor_icms",8,2);
            $table->decimal("percentual_icms",8,2);
            $table->string("origem_icms");
            $table->string("cst_icms");
            $table->string("modbc_icms");
            $table->decimal("vbc_icms",8,2);

            $table->decimal("valor_pis",8,2);
            $table->decimal("percentual_pis",8,2);
            $table->string("cst_pis");
            $table->decimal("vbc_pis",8,2);

            $table->decimal("valor_cofins",8,2);
            $table->decimal("percentual_cofins",8,2);
            $table->string("cst_cofins");
            $table->decimal("vbc_cofins",8,2);

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
        Schema::dropIfExists('loja_nfce_tributos');
    }
}
