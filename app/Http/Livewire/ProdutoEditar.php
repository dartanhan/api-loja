<?php

namespace App\Http\Livewire;

use App\Helpers\LivewireHelper;
use App\Http\Models\OrigemNfce;
use App\Http\Models\ProdutoImagem;
use App\Traits\ProdutoTrait;
use http\Client\Request;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use NumberFormatter;
use App\Http\Models\Produto;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\Fornecedor;
use App\Http\Models\Categoria;


class ProdutoEditar extends Component
{
    use ProdutoTrait;

    public $produto;
    public $produtos = [];
    public $variacoes = [];
    public $fornecedores = [];
    public $categorias = [];
    public $origem_nfces =[];
    public $codigoProduto; // nova propriedade
    public $produtoId;
    public $images = [];
    public array $pastasImagensProduto = [];   // imagens do produto pai
    public array $pastasImagensVariacoes = []; // imagens exclusivas de cada variação
    public $valor_produto;
    public $imagensExistentes = [];
    public $produtoCodigo;

    protected $listeners = [
        'pastasAtualizadasProduto' => 'setPastasImagensProduto',
        'pastasAtualizadasVariacao' => 'setPastasImagensVariacao',
        'deletarImagem'=>'deletarImagem','atualizar'=>'atualizarProduto',
        'imagensAtualizadas' => 'setImagens',
        'atualizarVariacoes' => 'setVariacoes', //trait
        'salvar','alterarStatusConfirmado','voltar',
        'syncAndSave' => 'salvar','removerImagem' => 'removerImagem'
    ];

//    protected $rules = [
//        'variacoes.*.descricao' => 'required|string|max:155',
//        'variacoes.*.fornecedor_id' => 'required|exists:loja_fornecedores,id',
//        'variacoes.*.categoria_id' => 'required|exists:loja_categorias,id',
//        'variacoes.*.origem_id' => 'required|exists:loja_produto_origem_nfces,codigo',
//    ];

    public function mount($id, string $context = 'produto', bool $multiple = false, ?string $variacaoKey = null, array $imagensExistentes = [])
    {
        $produto = Produto::with('variacoes.images','images')->findOrFail($id); //trás o PAI e sua relações
        $this->produto = $produto;
        $this->codigoProduto = $produto->codigo_produto;
        $this->produtoId = $produto->id;

        $this->produtos = [
            'id'=> $produto->id,
            'codigo_produto' => $produto->codigo_produto,
            'descricao' => $produto->descricao,
            'valor_produto' => $produto->valor_produto,
            'categoria_id' => $produto->categoria_id,
            'status' => $produto->status,
            'path' => !empty($produto['images']) ? $produto['images'][0]->path : null,
            'image_id' => !empty($produto['images']) ? $produto['images'][0]->id : null,
            'origem_id' => $produto->origem_id,
            'cest' =>  $produto->cest,
            'ncm' =>  $produto->ncm
        ];

        //carrego os dados das variações do produto para blade
        $this->variacoes = $this->carregdaDadosVariacao($produto);

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

    /**
     * @param $arquivoTemporario
     */
    public function setImagens($payload)
    {
        if ($payload['context'] === 'produto') {
            $this->pastasImagensProduto = [$payload['file']]; // só 1 imagem
        }

        if ($payload['context'] === 'variacao') {
            $key = $payload['variacaoKey'];
            if (!isset($this->pastasImagensVariacoes[$key])) {
                $this->pastasImagensVariacoes[$key] = [];
            }
            $this->pastasImagensVariacoes[$key][] = $payload['file'];
        }
    }

    public function salvar()
    {
     //   $this->validate();

        $formatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);

        // Validação de duplicidade de subcódigos
        $subcodigos = array_column($this->variacoes, 'subcodigo');
        $duplicados = array_diff_key($subcodigos, array_unique($subcodigos));

        if (!empty($duplicados)) {
            $this->addError('variacoes', 'Existem variações com subcódigo duplicado. Verifique antes de salvar.');
            return;
        }

        // Salva o produto pai
        //$produto = Produto::with('images')->findOrFail($this->produtoId);
        $data = [
                'descricao' => $this->produto['descricao'] ?? '',
                'valor_produto' => $this->produto['valor_produto'] ?? 0,
                'categoria_id' => $this->produto['categoria_id'] ?? 0,
                'status' => $this->produto['status'] ?? 0,
                'ncm' => $this->produto['ncm'] ?? 0,
                'cest' => $this->produto['cest'] ?? 0,
                'origem_id' => $this->produto['origem_id'] ?? 1
                ];


        Produto::where('id', $this->produtoId)->update($data);

        // Salva imagens do produto pai
        if (!empty($this->pastasImagensProduto)) {
            $this->salvarImagemV2($this->pastasImagensProduto, 'produto', $this->produtoId);
        }

        //dump($this->variacoes,$this->pastasImagensVariacoes);
       // dd();
        if (!empty($this->variacoes)) {
            foreach ($this->variacoes as $dados) {

                    $data = [
                        'products_id' =>  $this->produtoId,
                        'subcodigo' => $dados['subcodigo'] ?? '',
                        'variacao' => $dados['variacao'] ?? '',
                        'quantidade' => $dados['quantidade'] ?? 0,
                        'valor_varejo' => LivewireHelper::formatCurrencyToBD($dados['valor_varejo'], $this->NumberFormatter()) ?? 0,
                        'valor_produto' => LivewireHelper::formatCurrencyToBD($dados['valor_produto'], $this->NumberFormatter()) ?? 0,
                        'fornecedor' => $dados['fornecedor_id'] ,
                        'gtin' => $dados['gtin'] ?? 0,
                        'estoque' => $dados['estoque'] ?? 0,
                        'quantidade_minima' => $dados['quantidade_minima'] ?? 0,
                        'percentage' => $dados['percentage'] ?? 0,
                        'validade' => LivewireHelper::formatarData($dados['validade'])
                    ];

                   //dd($data ,  $variacao , $this->variacoes, $this->pastasImagensVariacoes[$dados['id']], $dados['id_temp']);
                    $matchThese = array('id' => $dados['id']);
                    $variacao = ProdutoVariation::updateOrCreate($matchThese, $data);


                    // Salva imagens da variação, se houver
                    if (!empty($this->pastasImagensVariacoes[$dados['id']])) {
                        $this->salvarImagemV2($this->pastasImagensVariacoes[$dados['id']], 'variacao', $variacao->id);
                    }
                }
        }


        //carregue o produto novamente com findOrFail (ou find) antes de acessar
        //$this->imagens = ProdutoVariation::with('images')->findOrFail($this->variacoes[0]['id'])->images;
//        // Recarrega imagens da primeira variação (ou do produto)
//        $this->imagens = !empty($this->variacoes[0]['id'])
//            ? ProdutoVariation::with('images')->findOrFail($this->variacoes[0]['id'])->images
//            :  $this->produto = Produto::with('images')->find($this->produto['id'])->toArray();

        //envia a mensagem no browser
        $this->dispatchBrowserEvent('livewire:event', [
            'type' => 'alert',
            'icon' => 'success',
            'message' => 'Produto e variações atualizados com sucesso!'
        ]);

        $produto = Produto::with('variacoes.images','images')->findOrFail($this->produtoId); //trás o PAI e sua relações
        //carrego os dados das variações do produto para blade
        $this->variacoes = $this->carregdaDadosVariacao($produto);
        //aciona o componente para atualizar as imagens
        $this->emitTo('produto-variacoes-form', 'imagemAtualizada', $this->variacoes);
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


    public function removerImagem($id)
    {
        $imagem = ProdutoImagem::find($id);
        if ($imagem) {
            Storage::delete('public/product/'.$imagem->produto_id.'/'.$imagem->path);
            $imagem->delete();
        }

        // Remove a imagem do array local
        $this->produto['images'] = array_filter($this->produto['images'], fn($img) => $img['id'] !== $id);

        // Mensagem de sucesso
        $this->dispatchBrowserEvent('swal:sucesso', [
            'message' => 'Imagem excluída com sucesso!'
        ]);
    }

    public function render()
    {
        //$this->emitTo('filepond-upload', 'imagemAtualizada', $this->imagensExistentes);
        return view('livewire.produto-editar')->layout('layouts.layout');
    }
}
