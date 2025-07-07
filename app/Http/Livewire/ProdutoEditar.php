<?php

namespace App\Http\Livewire;

use App\Http\Models\OrigemNfce;
use App\Http\Models\ProdutoImagem;
use App\Http\Models\TemporaryFile;
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
    public $produto;
    public $variacoes = [];
    public $fornecedores = [];
    public $categorias = [];
    public $origem_nfces =[];
    public $codigoPai; // nova propriedade
    public $produtoId;
    public $imagens = [];
    public array $pastasImagens = [];

    protected $listeners = ['setPastasImagens', 'salvar','deletarImagem'];

    public function mount(ProdutoVariation $produto)
    {

        $this->produto = $produto->produtoPai->toArray();// acessa o pai pela relação
        $this->produtoId = $this->produto['id'];
        $this->codigoPai = $this->produto['codigo_produto'] ?? '0000';
        $this->imagens = $produto->images; // Ajuste conforme relacionamento

        $this->variacoes = [
            [
                'id' => $produto->id,
                'subcodigo' => $produto->subcodigo,
                'variacao' => $produto->variacao,
                'quantidade' => $produto->quantidade,
                'valor_varejo' => number_format($produto->valor_varejo, 2, ',', '.'),
                'valor_produto' => number_format($produto->valor_produto, 2, ',', '.'),
                'gtin' => $produto->gtin,
                'estoque' => $produto->estoque,
                'quantidade_minima' => $produto->quantidade_minima,
                'percentage' => number_format($produto->percentage, 2, ',', '.'),
                'validade' => Carbon::parse($produto->validade)->format('d/m/Y'),
                'fornecedor_id' => $produto->fornecedor ??  $this->produto->fornecedor_id ?? null,
                'status' => $produto->status
            ]
        ];

        $this->fornecedores = Fornecedor::select('id', 'nome')->where('status', 1)->get()->toArray();

        $this->categorias = Categoria::select('id', 'nome')->where('status', 1)->get();

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
        $produto->ncm = $this->produto['ncm'] ?? 0;
        $produto->cest = $this->produto['cest'] ?? 0;
        $produto->origem_id = $this->produto['origem_id'] ?? 0;
        $produto->categoria_id = $this->produto['categoria_id'] ?? 0;
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
            }
        }

        // Após salvar o produto e variações...
        foreach ($this->pastasImagens as $folder) {
            $temporaryFile = TemporaryFile::where('folder', $folder)->first();

            if ($temporaryFile && Storage::exists('tmp/' . $folder . '/' . $temporaryFile->file)) {
                $file = $temporaryFile->file;
                $pathTemp = 'tmp/' . $folder . '/' . $file;
                $produtoVariacaoId = $this->variacoes[0]['id']; // ou conforme o loop se tiver várias
                $pathFinal = 'produtos/' . $produtoVariacaoId . '/' . $file;

                // Move
                Storage::makeDirectory('produtos/' . $produtoVariacaoId);
                Storage::move($pathTemp, $pathFinal);

                // Cria imagem associada ao produto ou variação (ajuste se for produto_id)
                ProdutoImagem::create([
                    'produto_variacao_id' => $this->variacoes[0]['id'], // ou ajuste para loop se forem várias
                    'path' => $pathFinal,
                    'produto_id' => null
                ]);

                // Limpa temporário
                Storage::deleteDirectory('tmp/' . $folder);
                $temporaryFile->delete();
            }
        }
        //carregue o produto novamente com findOrFail (ou find) antes de acessar
        $this->imagens = ProdutoVariation::with('images')->findOrFail($this->variacoes[0]['id'])->images;

        //envia a mensagem no browser
        $this->dispatchBrowserEvent('livewire:event', [
            'type' => 'alert',
            'icon' => 'success',
            'message' => 'Produto e variações atualizados com sucesso!'
        ]);
    }

    public function setPastasImagens($pastas)
    {
        $this->pastasImagens = $pastas ?? [];
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


    //acionado através d listener via js
    public function deletarImagem($imagemId)
    {
        $imagem = ProdutoImagem::find($imagemId);

        if ($imagem && Storage::disk('public')->exists($imagem->path)) {
            // Apaga do storage
            Storage::disk('public')->delete($imagem->path);
            // Remove do banco
            $imagem->delete();
            //carregue o produto novamente com findOrFail (ou find) antes de acessar
            $this->imagens = ProdutoVariation::with('images')->findOrFail($this->variacoes[0]['id'])->images;

            //envia a mensagem no browser
            $this->dispatchBrowserEvent('livewire:event', [
                'type' => 'alert',
                'icon' => 'success',
                'message' => 'Imagem deletada com sucesso!'
            ]);
            //atualiza a lista de imagens
            $this->dispatchBrowserEvent('livewire:event', [
                'type' => 'imagemRemovida',
                'id' => $imagem->id
            ]);

        }
    }


    public function render()
    {
        return view('livewire.produto-editar')->layout('layouts.layout');
    }
}
