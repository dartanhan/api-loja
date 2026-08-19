<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKnIntelligenceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('configuracoes_ia')) {
            Schema::create('configuracoes_ia', function (Blueprint $table) {
                $table->id();
                $table->string('provedor')->default('gemini');
                $table->text('api_key')->nullable();
                $table->string('modelo')->default('gemini-2.5-flash');
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ai_conversations')) {
            Schema::create('ai_conversations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('titulo')->default('Nova Conversa');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ai_messages')) {
            Schema::create('ai_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('conversation_id');
                $table->enum('role', ['user', 'assistant', 'system', 'tool']);
                $table->text('content');
                $table->json('tool_calls')->nullable();
                $table->timestamps();

                $table->foreign('conversation_id')->references('id')->on('ai_conversations')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('ai_insights')) {
            Schema::create('ai_insights', function (Blueprint $table) {
                $table->id();
                $table->enum('tipo', ['vendas', 'estoque', 'margem', 'geral']);
                $table->enum('severidade', ['baixa', 'media', 'alta', 'critica']);
                $table->string('titulo');
                $table->text('descricao');
                $table->json('dados')->nullable();
                $table->boolean('lido')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('ai_insights');
        Schema::dropIfExists('configuracoes_ia');
    }
}
