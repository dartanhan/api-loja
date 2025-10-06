<?php

namespace App\Http\Livewire;

use App\Helpers\LivewireHelper;
use App\Http\Models\Categoria;
use App\Http\Models\Fornecedor;
use App\Http\Models\OrigemNfce;
use App\Http\Models\Produto;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\TemporaryFile;
use App\Traits\ProdutoTrait;
use NumberFormatter;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProdutoCreate extends Component
{

    use WithFileUploads;
    use ProdutoTrait;
   // public $temporaryFiles = [];

    public $produto = [
        'codigo_produto' => '',
        'descricao' => '',
        'valor_produto' => '',
        'status' => 0,
        'categoria_id' => '',
        'origem_id' => ''
    ];

    protected $rules = [
        'produto.codigo_produto' => 'required|max:50',
        'produto.descricao' => 'required|string|max:255',
        'produto.valor_produto' => 'required|numeric|min:0',
        'produto.status' => 'required|boolean',
        'produto.categoria_id' => 'required|exists:loja_categorias,id',
        'produto.origem_id' => 'required|exists:loja_produto_origem_nfces,codigo',
    ];

    public $variacoes = [];
   // public $produtoImages = [];
   // public $imagemDestaque = null;
    public $fornecedores = [];
    public $origens =[];
    public $produtoImagem = null;
    public array $variacoesImagens = []; // ['SUBCODIGO_X' => [pastas...], 'SUBCODIGO_Y' => [...]];
    public $codigoProduto;
    public array $uploads = []; // usado apenas pelo Livewire para armazenar temporários
    public array $images  = []; // nosso array normalizado: sempre ['context','file','variacaoKey']


    protected $listeners = [
                            'imagensAtualizadas' => 'setImagens', //trait
                            'atualizarVariacoes' => 'setVariacoes', //trait
                            'salvar'             => 'salvar'];


    public function mount()
    {
        $this->produto['status'] = true;
        $lastCodigo = Produto::max('codigo_produto') ?? 0;
        $this->produto['codigo_produto'] = $lastCodigo + 1;
        $this->codigoProduto = $this->produto['codigo_produto'];

        $this->fornecedores = collect(); // esvazia antes
        $this->fornecedores = Fornecedor::select('id', 'nome')->where('status',1)->orderBy('nome','asc')->get();
        $this->categorias = collect(); // esvazia antes
        $this->categorias = Categoria::select('id', 'nome')->where('status',1)->orderBy('nome','asc')->get();
        $this->origens = OrigemNfce::get();

        //$this->loadTemporaryFiles();
    }

   /* public function loadTemporaryFiles()
    {
        $this->temporaryFiles = TemporaryFile::whereNull('folder')->get();
    }*/

    /*public function deleteTemporaryFile($folder)
    {
        $temp = TemporaryFile::where('folder', $folder)->first();
        if($temp) {
            Storage::disk('public')->deleteDirectory('tmp/'.$temp->folder);
            $temp->delete();
            $this->loadTemporaryFiles();
        }
    }*/

   /* public function atualizarPastasProduto($pastas)
    {
        logger()->info("Pai recebeu Produto", $pastas);
        $this->pastasImagensProduto = $pastas;
    }*/

  /*  public function atualizarPastasVariacao($payload)
    {
        $key = $payload['variacao_key'] ?? null;
        $pastas = $payload['pastas'] ?? [];

        if ($key) {
            $this->pastasImagensVariacoes[$key] = $pastas;
        }

        logger()->info("Pai recebeu Variação $key", $pastas);
    }*/

    /**
     * Salva o produto e variações
    */
    public function salvar()
    {

        $this->produto['valor_produto'] = LivewireHelper::formatCurrencyToBD($this->produto['valor_produto'],
            $this->NumberFormatter());

        $this->validate();

        /**
         * salva a produto
         🔹 Salva produto PAI
         * */
        $data = [
            'codigo_produto' => $this->produto['codigo_produto'],
            'descricao'      => $this->produto['descricao'] ?? '',
            'ncm'            => $this->produto['ncm'] ?? 0,
            'cest'           => $this->produto['cest'] ?? 0,
            'origem_id'      => (int)$this->produto['origem_id'] ?? 0,
            'categoria_id'   => (int)$this->produto['categoria_id'] ?? 0,
            'status'         => $this->produto['status'] ?? 0,
            'valor_produto'  => $this->produto['valor_produto'],
        ];

        /**
         * 🔹 0) Cria o PRODUTO PAI
         */
        $produto = Produto::create($data);


        /**
         * 🔹 1) Salva imagens do PRODUTO PAI
         */
      //
        //$imagensProduto = collect($this->images)->where('context', 'produto');
        if ($this->produtoImagem) {
            $this->salvarImagemV2($this->produtoImagem, 'produto', $produto->id);
        }

        /**
         * 🔹 2) Salva variações
         */
       // dump("2) Salva variações", $this->produto,$this->produtoImagem,$this->variacoes,$this->variacoesImagens);
       // dd();
        $mapVariacoes = []; // mapeia id temporário => id real
        if ($this->variacoes) {

            foreach ($this->variacoes as $v) {
                $data = [
                    'products_id' => $produto->id,
                    'subcodigo' => $v['subcodigo'] ?? '',
                    'variacao' => $v['variacao'] ?? '',
                    'quantidade' => $v['quantidade'] ?? 0,
                    'valor_varejo' => LivewireHelper::formatCurrencyToBD($v['valor_varejo'], $this->NumberFormatter()),
                    'valor_atacado' => LivewireHelper::formatCurrencyToBD($v['valor_atacado'], $this->NumberFormatter()),
                    'valor_atacado_10' => LivewireHelper::formatCurrencyToBD($v['valor_atacado'], $this->NumberFormatter()),
                    'valor_produto' => LivewireHelper::formatCurrencyToBD($v['valor_produto'], $this->NumberFormatter()),
                    'fornecedor_id' => $v['fornecedor_id'],
                    'gtin' => $v['gtin'] ?? null,
                    'estoque' => $v['estoque'] ?? 0,
                    'quantidade_minima' => $v['quantidade_minima'] ?? 0,
                    'percentage' => $v['percentage'] ?? 0,
                    'status' => $v['status'] ?? 0,
                    'validade' => LivewireHelper::formatarData($v['validade']),
                ];

                dump($data);
                dd();
                $variacao = ProdutoVariation::create($data);

                // 🔹 guarda relação entre ID temporário (UUID) e ID real
                $mapVariacoes[$v['id']] = $variacao->id;
            }

            // Agora salva as imagens de cada variação usando o mapeamento
            foreach ($this->variacoesImagens as $variacaoKey => $imagens) {
                if (!isset($mapVariacoes[$variacaoKey])) {
                    continue; // segurança: se não encontrou a variação, ignora
                }

                $variacaoId = $mapVariacoes[$variacaoKey];

                foreach ($imagens as $img) {
                    $this->salvarImagemV2($img, 'variacao', $variacaoId);
                }
            }

            $this->dispatchBrowserEvent('livewire:event', [
                'type'    => 'alert',
                'icon'    => 'success',
                'message' => 'Produto e variações cadastrados com sucesso!'
            ]);
        } else {
            $this->dispatchBrowserEvent('livewire:event', [
                'type'    => 'alert',
                'icon'    => 'success',
                'message' => 'Produto cadastrado com sucesso!'
            ]);
        }

        return redirect()->route('produtos.produtos_ativos');
    }

    public function render()
    {
        return view('livewire.produto-create')->layout('layouts.layout');;
    }
}
