<?php

namespace App\Http\Livewire;

use App\Http\Models\Categoria;
use App\Http\Models\Fornecedor;
use App\Http\Models\OrigemNfce;
use App\Http\Models\Produto;
use App\Http\Models\ProdutoImagem;
use App\Http\Models\ProdutoVariation;
use App\Http\Models\TemporaryFile;
use App\Traits\ProdutoTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProdutoCreate extends Component
{

    use WithFileUploads;
    use ProdutoTrait;
    public $temporaryFiles = [];

    public $produto = [
        'codigo_produto' => '',
        'descricao' => '',
        'valor_produto' => '',
        'status' => 0,
        'categoria_id' => ''
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
    public $produtoImages = [];
    public $imagemDestaque = null;
    public $fornecedores = [];
    public $origens =[];
    public $pastasImagens = [];
    protected $listeners = ['refreshTemporaryFiles' => 'loadTemporaryFiles', 'setPastasImagens' => 'setPastasImagens'];
    public $produtoCodigo;

    public function mount()
    {
        $this->produto['status'] = true;
        $lastCodigo = Produto::max('codigo_produto') ?? 0;
        $this->produto['codigo_produto'] = $lastCodigo + 1;
        $this->produtoCodigo = $this->produto['codigo_produto'];

        $this->fornecedores = collect(); // esvazia antes
        $this->fornecedores = Fornecedor::select('id', 'nome')->where('status',1)->orderBy('nome','asc')->get();
        $this->categorias = collect(); // esvazia antes
        $this->categorias = Categoria::select('id', 'nome')->where('status',1)->orderBy('nome','asc')->get();
        $this->origens = OrigemNfce::get();

        $this->loadTemporaryFiles();
    }

    public function loadTemporaryFiles()
    {
        $this->temporaryFiles = TemporaryFile::whereNull('folder')->get();
    }

    public function deleteTemporaryFile($folder)
    {
        $temp = TemporaryFile::where('folder', $folder)->first();
        if($temp) {
            Storage::disk('public')->deleteDirectory('tmp/'.$temp->folder);
            $temp->delete();
            $this->loadTemporaryFiles();
        }
    }

//    public function adicionarVariacao()
//    {
//        $seq = count($this->variacoes) + 1;
//        $subcodigo = $this->produto['codigo_produto'] . str_pad($seq, 2, '0', STR_PAD_LEFT);
//
//        $this->variacoes[] = [
//            'subcodigo' => $subcodigo,
//            'variacao' => '',
//            'quantidade' => 0,
//            'valor_varejo' => 0,
//            'status' => 1,
//            'imagens' => [],
//            'validade' => '',
//            'fornecedor_id' => ''
//        ];
//    }

    public function salvar()
    {

        $this->produto['valor_produto'] = str_replace(',', '.', preg_replace('/[^\d,]/', '', $this->produto['valor_produto'] ?? '0'));

        $this->validate();

        /**
         * salva a produto
         */
        $data = [
            'codigo_produto' => $this->produto['codigo_produto'],
            'descricao' => $this->produto['descricao'] ?? '',
            'ncm' => $this->produto['ncm'] ?? 0,
            'cest' => $this->produto['cest'] ?? 0,
            'origem_id' => (int)$this->produto['origem_id'] ?? 0,
            'categoria_id' => (int)$this->produto['categoria_id'] ?? 0,
            'status' => $this->produto['status'] ?? 0,
            'valor_produto' => $this->produto['valor_produto']
        ];

       $produto = Produto::create($data);

        //este valor vem do pond.setOptions() em util.js
        foreach ($this->pastasImagens as $folder) {
            $temporaryFile = TemporaryFile::where('folder', $folder)->first();

            if ($temporaryFile && Storage::disk('public')->exists('tmp/' . $folder . '/' . $temporaryFile->file)) {
                $file = $temporaryFile->file;
                $pathTemp = 'tmp/' . $folder . '/' . $file;
                $pathFinal = 'product/' . $produto->id . '/' . $file;

                // Move arquivo
                Storage::makeDirectory('product/' . $produto->id);
                Storage::disk('public')->move($pathTemp, $pathFinal);

                // Registra no banco como imagem do produto pai
                ProdutoImagem::create([
                    'produto_id' => $produto->id,
                    'produto_variation_id' => null,  // ou omite se não for obrigatório
                    'path' => $file
                ]);

                // Remove temporário
                Storage::deleteDirectory('tmp/' . $folder);
                $temporaryFile->delete();
            }
        }

        /**
         * salva as variações
        */
        if($this->variacoes) {
            foreach ($this->variacoes as $v) {

                $data = [
                    'subcodigo' => $v['subcodigo'] ?? '',
                    'variacao' => $v['variacao'] ?? '',
                    'quantidade' => $v['quantidade'] ?? 0,
                    'valor_varejo' => $v['valor_varejo'] ?? 0,
                    'valor_produto' => $v['valor_produto'] ?? 0,
                    'fornecedor_id' => $v['fornecedor_id'] ?? null,
                    'gtin' => $v['gtin'] ?? 0,
                    'estoque' => $v['estoque'] ?? 0,
                    'quantidade_minima' => $v['quantidade_minima'] ?? 0,
                    'percentage' => $v['percentage'] ?? 0,
                    'status' => $v['status'] ?? 0,
                    'produto_id' => $produto->id
                ];

                // Trata validade
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $v['validade'] ?? '')) {
                    $data['validade'] = Carbon::createFromFormat('d/m/Y', $v['validade'])->format('Y-m-d');
                } else {
                    $data['validade'] = '0000-00-00';
                }

                $variacao = ProdutoVariation::create($data);


                // Se houver pastas temporárias associadas à variação
                if (!empty($v['pastasImagens'])) {
                    foreach ($v['pastasImagens'] as $folder) {
                        $temporaryFile = TemporaryFile::where('folder', $folder)->first();

                        if ($temporaryFile && Storage::disk('public')->exists('tmp/' . $folder . '/' . $temporaryFile->file)) {
                            $file = $temporaryFile->file;
                            $pathTemp = 'tmp/' . $folder . '/' . $file;
                            $pathFinal = 'produtos/' . $produto->id . '/variacoes/' . $variacao->id . '/' . $file;

                            Storage::makeDirectory('produtos/' . $produto->id . '/variacoes/' . $variacao->id);
                            Storage::move($pathTemp, $pathFinal);

                            ProdutoImagem::create([
                                'produto_id' => null,
                                'produto_variation_id' => $variacao->id,
                                'path' => $pathFinal
                            ]);

                            // Limpa os temporários
                            Storage::deleteDirectory('tmp/' . $folder);
                            $temporaryFile->delete();
                        }
                    }
                }
            }
            //envia a mensagem no browser
            $this->dispatchBrowserEvent('livewire:event', [
                'type' => 'alert',
                'icon' => 'success',
                'message' => 'Produto e variações cadastrados com sucesso!'
            ]);
        }else{
            //envia a mensagem no browser
            $this->dispatchBrowserEvent('livewire:event', [
                'type' => 'alert',
                'icon' => 'success',
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
