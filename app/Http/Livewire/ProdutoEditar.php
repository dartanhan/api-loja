<?php

namespace App\Http\Livewire;

use App\Http\Models\OrigemNfce;
use App\Http\Models\ProdutoImagem;
use App\Http\Models\TemporaryFile;
use App\Traits\ProdutoTrait;
use http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

use App\Http\Models\Produto;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\Fornecedor;
use App\Http\Models\Categoria;


class ProdutoEditar extends Component
{
    use ProdutoTrait;

    public $produto;
    public $variacoes = [];
    public $fornecedores = [];
    public $categorias = [];
    public $origem_nfces =[];
    public $codigoPai; // nova propriedade
    public $produtoId;
    public $imagens = [];
    public array $pastasImagensProduto = [];   // imagens do produto pai
    public array $pastasImagensVariacoes = []; // imagens exclusivas de cada variação
    public $valor_produto;
    public $imagensExistentes = [];
    public $produtoCodigo;

    protected $listeners = [
        'pastasAtualizadasProduto' => 'setPastasImagensProduto',
        'pastasAtualizadasVariacao' => 'setPastasImagensVariacao',
        'deletarImagem'=>'deletarImagem','atualizar'=>'atualizarProduto',
        'imagemAtualizada' => 'carregarImagens',
        'salvar','alterarStatusConfirmado','voltar'];

    public function mount($id, $tipo = 'produto')
    {
        if ($tipo === 'variacao') {
            $variacao = ProdutoVariation::with('produtoPai')->findOrFail($id);

//            dump($variacao);
//            die();

            $this->produto = $variacao->produtoPai->toArray();// acessa o pai pela relação
            $this->produtoId = $this->produto['id'];
            $this->codigoPai = $this->produto['codigo_produto'] ?? '0000';
            $this->imagens = $variacao->images; // Ajuste conforme relacionamento
            $this->valor_produto = $this->produto['valor_produto'];

            $this->variacoes = [
                [
                    'id' => $variacao->id,
                    'subcodigo' => $variacao->subcodigo,
                    'variacao' => $variacao->variacao,
                    'quantidade' => $variacao->quantidade,
                    'valor_varejo' => number_format($variacao->valor_varejo, 2, ',', '.'),
                    'valor_produto' => number_format($variacao->valor_produto, 2, ',', '.'),
                    'gtin' => $variacao->gtin,
                    'estoque' => $variacao->estoque,
                    'quantidade_minima' => $variacao->quantidade_minima,
                    'percentage' => number_format($variacao->percentage, 2, ',', '.'),
                    'validade' => Carbon::parse($variacao->validade)->format('d/m/Y'),
                    'fornecedor_id' => $variacao->fornecedor ??  $this->produto->fornecedor_id ?? null,
                    'status' => $variacao->status
                ]
            ];

        }else{
            $produto = Produto::with('variances','images')->findOrFail($id);

            $this->produto = $produto->toArray();
            $this->produtoId = $produto->id;
            $this->codigoPai = $this->produto['codigo_produto'];

            $this->variacoes = $produto->variances->map(fn($v) => [
                'id' => $v->id,
                'subcodigo' => $v->subcodigo,
                'variacao' => $v->variacao,
                'quantidade' => $v->quantidade,
                // ...
            ])->toArray();

            $this->imagensExistentes = $this->produto['images'] ?? [];

        }

        //$this->fornecedores = Fornecedor::select('id', 'nome')->where('status', 1)->get()->toArray();
        $this->fornecedores = Fornecedor::select('id', 'nome')->where('status',1)->orderBy('nome','asc')->get();

        $this->categorias = Categoria::select('id', 'nome')->where('status',1)->orderBy('nome','asc')->get();

        $this->origem_nfces = OrigemNfce::get();
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
            'gtin' => '',
            'fornecedor_id' => $produto->fornecedor ?? null, // ← aqui também
            'status' => null
        ];
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
        $produto->ncm = $this->produto['ncm'] ?? 0;
        $produto->cest = $this->produto['cest'] ?? 0;
        $produto->origem_id = $this->produto['origem_id'] ?? 0;
        $produto->categoria_id = $this->produto['categoria_id'] ?? 0;
        $produto->status = $this->produto['status'] ?? 0;
        $produto->valor_produto = $this->produto['valor_produto'] ?? 0;
        $produto->save();

        // Salva imagens do produto pai
        $this->salvarImagens($this->pastasImagensProduto, 'produto', $produto->id);

        if (!empty($dados['variacoes'])) {
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
                    $variacao->gtin = $dados['gtin'] ?? 0;
                    $variacao->estoque = $dados['estoque'] ?? 0;
                    $variacao->quantidade_minima = $dados['quantidade_minima'] ?? 0;
                    $variacao->percentage = $dados['percentage'] ?? 0;

                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dados['validade'])) {
                        $variacao->validade = Carbon::createFromFormat('d/m/Y', $dados['validade'])->format('Y-m-d');
                    } else {
                        $variacao->validade = '0000-00-00';
                    }
                    $variacao->save();

                    // Salva imagens da variação, se houver
                    if (!empty($this->pastasImagensVariacoes[$this->variacoes[0]['id']])) {
                        $this->salvarImagens($this->pastasImagensVariacoes[$this->variacoes[0]['id']], 'variacao', $this->variacoes[0]['id']);
                    }
                }
            }

        }

        //carregue o produto novamente com findOrFail (ou find) antes de acessar
        //$this->imagens = ProdutoVariation::with('images')->findOrFail($this->variacoes[0]['id'])->images;
        // Recarrega imagens da primeira variação (ou do produto)
        $this->imagens = !empty($this->variacoes[0]['id'])
            ? ProdutoVariation::with('images')->findOrFail($this->variacoes[0]['id'])->images
            : $produto->images;

        //envia a mensagem no browser
        $this->dispatchBrowserEvent('livewire:event', [
            'type' => 'alert',
            'icon' => 'success',
            'message' => 'Produto e variações atualizados com sucesso!'
        ]);

        // Emite para o componente FilepondUpload atualizar a lista
        $this->emitTo('filepond-upload', 'imagemAtualizada', $this->imagensExistentes);
    }


    public function uploadImagem(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('produtos/' . $this->produto->id, 'public');

            // Salvar no banco se desejar
            $this->produto->imagens()->create([
                'caminho' => $path
            ]);

            return response()->json(['path' => $path], 200);
        }

        return response()->json(['error' => 'Nenhum arquivo enviado'], 400);
    }


    //voltar tela de lista de produtos
    public function voltar()
    {
        return redirect()->route('produtos.produtos_ativos');
    }

    //ao deletar a imagem do API atualiza o componente para exibir o filepond
    public function atualizarProduto()
    {
        $produto['images'][0] = []; // se estiver usando Model
    }



    public function render()
    {
        //$this->emitTo('filepond-upload', 'imagemAtualizada', $this->imagensExistentes);
        return view('livewire.produto-editar')->layout('layouts.layout');
    }
}
