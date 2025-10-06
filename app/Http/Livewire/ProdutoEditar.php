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
    public $produtoImagem = null;
    public array $variacoesImagens = [];
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

        $produto = Produto::with('variacoes.images','images')->findOrFail($id); //trás o PAI e as varições se tiver

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
            'path' => data_get($produto, 'images.0.path'),//retorna null se não exitir o images ou estiver vazio
            'image_id' => data_get($produto, 'images.0.id'),//retorna null se não exitir o images ou estiver vazio
            'origem_id' => $produto->origem_id,
            'cest' =>  $produto->cest,
            'ncm' =>  $produto->ncm
        ];

        //carrego os dados das variações do produto para blade
        $this->variacoes = $this->carregdaDadosVariacao($produto);

        $this->fornecedores = Fornecedor::select('id', 'nome')->where('status',1)->orderBy('nome','asc')->get();

        $this->categorias = Categoria::select('id', 'nome')->where('status',1)->orderBy('nome','asc')->get();

        $this->origem_nfces = OrigemNfce::get();

    }

    /**
     * @param $arquivoTemporario
     */
    public function salvar()
    {
        $formatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);

        // 1) Validação de duplicidade
        $subcodigos = array_column($this->variacoes, 'subcodigo');
        $duplicados = array_diff_key($subcodigos, array_unique($subcodigos));

        if (!empty($duplicados)) {
            $this->addError('variacoes', 'Existem variações com subcódigo duplicado. Verifique antes de salvar.');
            return;
        }

        // 2) Atualiza produto pai
        $data = [
            'descricao' => $this->produto['descricao'] ?? '',
            'valor_produto' => $this->produto['valor_produto'] ?? 0,
            'categoria_id' => $this->produto['categoria_id'] ?? 0,
            'status' => $this->produto['status'] ?? 0,
            'ncm' => $this->produto['ncm'] ?? 0,
            'cest' => $this->produto['cest'] ?? 0,
            'origem_id' => $this->produto['origem_id'] ?? 1
        ];

        //dump($this->variacoes);
       // dd();
        Produto::where('id', $this->produtoId)->update($data);

        // 3) Imagens do produto pai
        if ($this->produtoImagem) {
            // Salva
            $this->salvarImagemV2($this->produtoImagem, 'produto', $this->produtoId);
        }

        // 4) Variações
        if (!empty($this->variacoes)) {
            foreach ($this->variacoes as $dados) {

                    $data = [
                        'products_id' => $this->produtoId,
                        'subcodigo' => $dados['subcodigo'] ?? '',
                        'variacao' => $dados['variacao'] ?? '',
                        'quantidade' => $dados['quantidade'] ?? 0,
                        'valor_varejo' => LivewireHelper::formatCurrencyToBD($dados['valor_varejo'], $this->NumberFormatter()) ?? 0,
                        'valor_atacado' => LivewireHelper::formatCurrencyToBD($dados['valor_atacado'], $this->NumberFormatter()) ?? 0,
                        'valor_atacado_10' => LivewireHelper::formatCurrencyToBD($dados['valor_atacado'], $this->NumberFormatter()) ?? 0,
                        'valor_produto' => LivewireHelper::formatCurrencyToBD($dados['valor_produto'], $this->NumberFormatter()) ?? 0,
                        'fornecedor' => $dados['fornecedor_id'],
                        'gtin' => $dados['gtin'] ?? 0,
                        'estoque' => $dados['estoque'] ?? 0,
                        'quantidade_minima' => $dados['quantidade_minima'] ?? 0,
                        'percentage' => $dados['percentage'] ?? 0,
                        'validade' => LivewireHelper::formatarData($dados['validade'])
                    ];

                    $variacao = ProdutoVariation::updateOrCreate(
                        ['id' => $dados['id']],
                        $data
                    );
                    if (isset($this->variacoesImagens[$dados['id']])) {
                        foreach ($this->variacoesImagens[$dados['id']] as $image) {
                            // Aqui $image já é a string correta
                            $this->salvarImagemV2($image, 'variacao', $variacao->id);
                        }
                    }

            }
            $this->dispatchBrowserEvent('filepond:reset', [
                'context' => 'variacao',
                'variacaoKey' => $dados['id'], // ou o id da variação salva
            ]);
        }

        // 5) Feedback
        $this->dispatchBrowserEvent('livewire:event', [
            'type' => 'alert',
            'icon' => 'success',
            'message' => 'Produto e variações atualizados com sucesso!'
        ]);


        // 6) Recarregar estado atualizado
        $this->produto = Produto::with('variacoes.images', 'images')->findOrFail($this->produtoId);
        $this->variacoes = $this->carregdaDadosVariacao($this->produto);

        $this->emitTo('produto-variacoes-form', 'imagemAtualizada', $this->variacoes);
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

    /**
     * @param $imageId
     * @param array $destino
     * @param int $produtoId
     */
    public function removerImagem(int $imageId,array $destino, int $produtoId)
    {
        $imagem = ProdutoImagem::find($imageId);
        $dest = null;

        if ($imagem) {
            //monta o destino da imagem
            $dest = ($destino['destino'] == 'product') ?
                $destino['destino'] . '/' . $imagem->produto_id . '/' . $imagem->path
                :
                $destino['destino'] . '/' . $imagem->produto_variacao_id . '/' . $imagem->path;

            //deleta a imagem
            Storage::disk('public')->delete($dest);

            //deleta do banco
            ProdutoImagem::where('id', $imagem->id)->delete();

            //monta destino do diretrio para apagar caso seja vazio
            $diretorio = ($destino['destino'] == 'product')
                ?
                $destino['destino'] . '/' . $imagem->produto_id
                :
                $destino['destino'] . '/' . $imagem->produto_variacao_id;

            //se for vazio, apaga o diretorio
            if (empty(Storage::disk('public')->files($diretorio))) {
                Storage::disk('public')->deleteDirectory($diretorio);
            }

            // Mensagem de sucesso
            $this->dispatchBrowserEvent('livewire:event', [
                'type' => 'alert',
                'icon' => 'success',
                'message' => 'Imagem removida com sucesso!'
            ]);


            // 1) Recarregar estado atualizado para blade
            $this->produto = Produto::with('variacoes.images', 'images')->findOrFail($produtoId);
            $this->variacoes = $this->carregdaDadosVariacao($this->produto);

            //atualiza o compnete de variações
            $this->emitTo('produto-variacoes-form', 'imagemAtualizada', $this->variacoes);
        }
    }

    public function render()
    {
        //$this->emitTo('filepond-upload', 'imagemAtualizada', $this->imagensExistentes);
        return view('livewire.produto-editar')->layout('layouts.layout');
    }
}
