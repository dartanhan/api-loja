<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class KnIntelligenceSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        $duasSemanasAtras = Carbon::now()->subDays(14);

        // Limpeza dos dados de testes anteriores (Evitar duplicate_key)
        $vendasTestes = DB::table('loja_vendas')->whereIn('codigo_venda', ['VND-TESTE-001', 'VND-TESTE-002'])->pluck('id');
        DB::table('loja_vendas_produtos')->whereIn('venda_id', $vendasTestes)->delete();
        DB::table('loja_vendas')->whereIn('id', $vendasTestes)->delete();

        $produtosTestes = DB::table('loja_produtos_new')->whereIn('codigo_produto', ['TESTE-A', 'TESTE-B', 'TESTE-C', 'TESTE-D'])->pluck('id');
        $variacoesTestes = DB::table('loja_produtos_variacao')->whereIn('products_id', $produtosTestes)->pluck('id');
        DB::table('loja_produtos_controle')->whereIn('products_variation_id', $variacoesTestes)->delete();
        DB::table('loja_produtos_variacao')->whereIn('products_id', $produtosTestes)->delete();
        DB::table('loja_produtos_new')->whereIn('id', $produtosTestes)->delete();

        // Garantir Fornecedor para evitar erro
        $fornecedor = DB::table('loja_fornecedores')->first();
        if (!$fornecedor) {
            $fornecedorId = DB::table('loja_fornecedores')->insertGetId([
                'nome' => 'Fornecedor Teste KN',
                'status' => 1,
            ]);
        } else {
            $fornecedorId = $fornecedor->id;
        }

        // Garantir que a loja base exista
        if (!DB::table('loja_lojas')->where('id', 1)->exists()) {
            DB::table('loja_lojas')->insert([
                'id' => 1,
                'nome' => 'Loja Matriz (Teste Inteligência)',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 1. Criar Cenário A: Variação Normal (Estoque positivo, Custo Conhecido, Com Vendas)
        $produtoA = DB::table('loja_produtos_new')->insertGetId([
            'codigo_produto' => 'TESTE-A',
            'descricao' => '[KN Teste] Produto de Giro Normal',
            'categoria_id' => 1,
            'fornecedor_id' => $fornecedorId,
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        $varA = DB::table('loja_produtos_variacao')->insertGetId([
            'products_id' => $produtoA,
            'subcodigo' => 'TESTE-A-VAR',
            'variacao' => 'Tamanho Único',
            'quantidade' => 100, // Estoque atual
            'quantidade_minima' => 10,
            'valor_varejo' => 50.00,
            'created_at' => $now,
            'updated_at' => $now,
            'gtin' => '0000000000000'
        ]);

        DB::table('loja_produtos_controle')->insert([
            'products_variation_id' => $varA,
            'valor_custo' => 20.00, // Margem de 60%
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Criar Cenário B: Produto Parado (Estoque positivo, Custo Conhecido, SEM VENDAS)
        $produtoB = DB::table('loja_produtos_new')->insertGetId([
            'codigo_produto' => 'TESTE-B',
            'descricao' => '[KN Teste] Produto Parado no Estoque',
            'categoria_id' => 1,
            'fornecedor_id' => $fornecedorId,
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        $varB = DB::table('loja_produtos_variacao')->insertGetId([
            'products_id' => $produtoB,
            'subcodigo' => 'TESTE-B-VAR',
            'variacao' => 'Tamanho Único',
            'quantidade' => 50, // Estoque parado
            'quantidade_minima' => 5,
            'valor_varejo' => 30.00,
            'created_at' => $now,
            'updated_at' => $now,
            'gtin' => '0000000000001'
        ]);

        DB::table('loja_produtos_controle')->insert([
            'products_variation_id' => $varB,
            'valor_custo' => 15.00,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Criar Cenário C: Ruptura (Estoque zero, Custo Conhecido, COM VENDAS passadas)
        $produtoC = DB::table('loja_produtos_new')->insertGetId([
            'codigo_produto' => 'TESTE-C',
            'descricao' => '[KN Teste] Produto em Ruptura (Zero Estoque)',
            'categoria_id' => 1,
            'fornecedor_id' => $fornecedorId,
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        $varC = DB::table('loja_produtos_variacao')->insertGetId([
            'products_id' => $produtoC,
            'subcodigo' => 'TESTE-C-VAR',
            'variacao' => 'Tamanho Único',
            'quantidade' => 0, // Estoque Zero
            'quantidade_minima' => 10,
            'valor_varejo' => 40.00,
            'created_at' => $now,
            'updated_at' => $now,
            'gtin' => '0000000000002'
        ]);

        DB::table('loja_produtos_controle')->insert([
            'products_variation_id' => $varC,
            'valor_custo' => 18.00,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 4. Criar Cenário D: Sem Custo Registrado
        $produtoD = DB::table('loja_produtos_new')->insertGetId([
            'codigo_produto' => 'TESTE-D',
            'descricao' => '[KN Teste] Produto Sem Custo (Erro de Cadastro)',
            'categoria_id' => 1,
            'fornecedor_id' => $fornecedorId,
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        $varD = DB::table('loja_produtos_variacao')->insertGetId([
            'products_id' => $produtoD,
            'subcodigo' => 'TESTE-D-VAR',
            'variacao' => 'Tamanho Único',
            'quantidade' => 20, 
            'quantidade_minima' => 5,
            'valor_varejo' => 90.00,
            'created_at' => $now,
            'updated_at' => $now,
            'gtin' => '0000000000003'
        ]);
        // NÃO inserimos na tabela de controle de custo de propósito para testar a blindagem.

        // ==========================================
        // GERAR VENDAS PARA OS PRODUTOS (Cenário A, C, D)
        // ==========================================

        // Venda 1: Vendeu Produto A (10 unidades) e Produto C (5 unidades) há 2 semanas
        $venda1Id = DB::table('loja_vendas')->insertGetId([
            'codigo_venda' => 'VND-TESTE-001',
            'loja_id' => 1,
            'valor_total' => (10 * 50.00) + (5 * 40.00),
            'created_at' => $duasSemanasAtras,
            'updated_at' => $duasSemanasAtras,
        ]);

        DB::table('loja_vendas_produtos')->insert([
            [
                'venda_id' => $venda1Id,
                'produto_id' => $produtoA,
                'variacao_id' => $varA,
                'codigo_produto' => 'TESTE-A',
                'descricao' => '[KN Teste] Produto de Giro Normal',
                'valor_produto' => 50.00,
                'quantidade' => 10,
                'percentual_desconto' => 0,
                'troca' => false,
                'loja_venda_id_troca' => $venda1Id,
            ],
            [
                'venda_id' => $venda1Id,
                'produto_id' => $produtoC,
                'variacao_id' => $varC,
                'codigo_produto' => 'TESTE-C',
                'descricao' => '[KN Teste] Produto em Ruptura (Zero Estoque)',
                'valor_produto' => 40.00,
                'quantidade' => 5,
                'percentual_desconto' => 0,
                'troca' => false,
                'loja_venda_id_troca' => $venda1Id,
            ]
        ]);

        // Venda 2: Vendeu Produto D (Sem custo - 2 unidades) Hoje
        $venda2Id = DB::table('loja_vendas')->insertGetId([
            'codigo_venda' => 'VND-TESTE-002',
            'loja_id' => 1,
            'valor_total' => 2 * 90.00,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('loja_vendas_produtos')->insert([
            [
                'venda_id' => $venda2Id,
                'produto_id' => $produtoD,
                'variacao_id' => $varD,
                'codigo_produto' => 'TESTE-D',
                'descricao' => '[KN Teste] Produto Sem Custo (Erro de Cadastro)',
                'valor_produto' => 90.00,
                'quantidade' => 2,
                'percentual_desconto' => 0,
                'troca' => false,
                'loja_venda_id_troca' => $venda2Id,
            ]
        ]);
        
        $this->command->info('Dados de teste do KN Intelligence populados com sucesso!');
    }
}
