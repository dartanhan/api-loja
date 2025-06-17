<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VendaTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
//    public function test_example()
//    {
//        $response = $this->get('/');
//
//        $response->assertStatus(200);
//    }

    public function testVendaEhRegistradaComSucesso()
    {
        $response = $this->postJson('/api/vendas', [
            'codigo_venda' => 'V001',
            'loja_id' => 1,
            'valor_total' => 150.00,
            'clienteModel' => ['id' => 1],
            'usuario_id' => 1,
            'tipoEntregaCliente' => 2,
            'forma_entrega_id' => 3,
            'data' => now(),
            'produtos' => [
                [
                    'codigo_produto' => 'P001',
                    'descricao' => 'Produto de Teste',
                    'valor_produto' => 50.00,
                    'quantidade' => 3,
                    'fornecedor_id' => 1,
                    'categoria_id' => 2,
                    'variacao_id' => 1,
                    'troca' => false
                ]
            ],
            'pagamentos' => [
                ['id' => 1, 'valor_pagamento' => 150.00]
            ],
            'desconto' => [
                'percentual' => 0,
                'valor_recebido' => 150.00,
                'valor_desconto' => 0,
            ]
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Venda registrada com sucesso.'
            ]);

        $this->assertDatabaseHas('vendas', ['codigo_venda' => 'V001']);
        $this->assertDatabaseHas('vendas_produtos', ['codigo_produto' => 'P001']);
    }

}
