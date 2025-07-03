<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Http\Models\Produto;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\Fornecedor;
use App\Http\Models\Categoria;


class ProdutoEditar extends Component
{
    public $produto;
    public $variacoes = [];
    public $fornecedores = [];
    public $codigoPai; // nova propriedade
    public $produtoId;

    public function mount(ProdutoVariation $produto)
    {

        $this->produto = $produto->produtoPai->toArray();// acessa o pai pela relação
        $this->produtoId = $this->produto['id'];
        $this->codigoPai = $this->produto['codigo_produto'] ?? '0000';

        $this->variacoes = [
            [
                'id' => $produto->id,
                'subcodigo' => $produto->subcodigo,
                'variacao' => $produto->variacao,
                'quantidade' => $produto->quantidade,
                'valor_varejo' => $produto->valor_varejo,
                'valor_produto' => $produto->valor_produto,
                'fornecedor_id' => $produto->fornecedor ??  $this->produto->fornecedor_id ?? null, // ← ajustado aqui
            ]
        ];

        $this->fornecedores = Fornecedor::select('id', 'nome')
            ->where('status', 1)
            ->get()
            ->toArray();
    }


    public function adicionarVariacao()
    {

         $codigoPai = $this->codigoPai ?? '0000';

        // 1. Busca o maior subcodigo no banco
        $ultimoDoBanco = ProdutoVariation::select('subcodigo')->where('subcodigo', 'LIKE', $codigoPai . '%')
            ->orderByDesc('subcodigo')->value('subcodigo');

        $maiorSufixoBanco = 0;
        if ($ultimoDoBanco && strlen($ultimoDoBanco) > strlen($codigoPai)) {
            $sufixo = substr($ultimoDoBanco, strlen($codigoPai));
            if (is_numeric($sufixo)) {
                $maiorSufixoBanco = (int) $sufixo;
            }
        }

        // 2. Busca o maior sufixo entre as variações em memória (tela)
        $maiorSufixoTela = 0;
        foreach ($this->variacoes as $v) {
            if (isset($v['subcodigo']) && str_starts_with($v['subcodigo'], $codigoPai)) {
                $sufixo = substr($v['subcodigo'], strlen($codigoPai));
                if (is_numeric($sufixo)) {
                    $maiorSufixoTela = max($maiorSufixoTela, (int) $sufixo);
                }
            }
        }

        // 3. Usa o maior dos dois
        $novoSufixo = max($maiorSufixoBanco, $maiorSufixoTela) + 1;
        $novoSubcodigo = $codigoPai . str_pad($novoSufixo, 2, '0', STR_PAD_LEFT);

        $this->variacoes[] = [
            'id' => null,
            'subcodigo' => $novoSubcodigo,
            'variacao' => '',
            'quantidade' => 0,
            'valor_varejo' => '',
            'valor_produto' => '',
            'fornecedor_id' => $produto->fornecedor ?? null, // ← aqui também
        ];
    }

    public function removerVariacao($index)
    {
        unset($this->variacoes[$index]);
        $this->variacoes = array_values($this->variacoes); // reindexa o array
    }


    public function salvar()
    {
        // Validação de duplicidade de subcódigos
        $subcodigos = array_column($this->variacoes, 'subcodigo');
        $duplicados = array_diff_key($subcodigos, array_unique($subcodigos));

        if (!empty($duplicados)) {
            $this->addError('variacoes', 'Existem variações com subcódigo duplicado. Verifique antes de salvar.');
            return;
        }

        // Salva o produto pai
        $produto = Produto::findOrFail($this->produtoId);
        $produto->descricao = $this->produto['descricao'] ?? '';
        $produto->status = $this->produto['status'] ?? 0;
        $produto->save();

        foreach ($this->variacoes as $dados) {
            if (isset($dados['id']) && $dados['id']) {
                $variacao = ProdutoVariation::find($dados['id']);
            } else {
                $variacao = new ProdutoVariation();
                $variacao->products_id = $this->produtoId;
            }

            if ($variacao) {
                $variacao->subcodigo = $dados['subcodigo'] ?? '';
                $variacao->variacao = $dados['variacao'] ?? '';
                $variacao->quantidade = $dados['quantidade'] ?? 0;
                $variacao->valor_varejo = $dados['valor_varejo'] ?? 0;
                $variacao->valor_produto = $dados['valor_produto'] ?? 0;
                $variacao->fornecedor = $dados['fornecedor_id'] ?? null;
                $variacao->save();
            }
        }

        session()->flash('success', 'Produto e variações atualizados com sucesso!');
    }

    public function render()
    {
        return view('livewire.produto-editar')->layout('layouts.layout');
    }
}
