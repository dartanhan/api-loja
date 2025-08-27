<?php

namespace App\Traits;

use App\Http\Models\Produto;
use App\Http\Models\ProdutoVariation;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\TemporaryFile;
use App\Http\Models\ProdutoImagem;

trait ProdutoTrait
{
    /**
     * acionado via listener ao clicar no toogle desativa ou ativa o PAI e suas variações
     * @param $data
     */
    public function alterarStatusConfirmado($data)
    {
        $produto = Produto::with('variacoes')->findOrFail($data['produtoId']);
        if ($data['tipo'] === 'produto') {
            if ($produto->status) {
                // Vai desativar
                $produto->status = 0;
                $produto->save();

                foreach ($produto->variacoes as $v) {
                    $v->status = 0;
                    $v->save();
                }

                $this->dispatchBrowserEvent('status-alterado',['text'=>'Produto e variações desativados com sucesso.']);
            } else {
                // Apenas ativa direto
                $produto->status = 1;
                $produto->save();
                $this->dispatchBrowserEvent('status-alterado', ['text' => 'Produto ativado com sucesso.']);
            }
        }else{

            $variacoesAtivas = $produto->variacoes->where('status', 1)->count();

            if ($variacoesAtivas === 1 && $data['tipo'] !== 'ultima_variacao') {

                $this->dispatchBrowserEvent('confirmarDesativacaoStatus', [
                    'title' => 'Última Variação Ativa',
                    'text' => 'Ao desativar também desativará o produto pai, ok?',
                    'produtoId' => $data['produtoId'],
                    'tipo' => 'ultima_variacao',
                    'icon' => 'warning'
                ]);

            } elseif ($data['tipo'] === 'ultima_variacao') {
                $produto = Produto::with('variacoes')->findOrFail($data['produtoId']);
                $produto->status = 0;
                $produto->save();

                foreach ($produto->variacoes as $v) {
                    $v->status = 0;
                    $v->save();
                }

                $this->dispatchBrowserEvent('status-alterado',['text'=>'Produto e variações desativados.']);
            } else {
                $variacao = ProdutoVariation::findOrFail($data['variacaoId']);
                $variacao->status = 0;
                $variacao->save();
                $this->dispatchBrowserEvent('status-alterado',['text'=>'Variação desativada com sucesso.']);
            }

        }
    }


    public function removerVariacao($index)
    {
        unset($this->variacoes[$index]);
        $this->variacoes = array_values($this->variacoes); // reindexa o array

        // Recalcula os subcódigos conforme nova ordem
        foreach ($this->variacoes as $i => &$variacao) {
            $numeroSequencial = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            $variacao['subcodigo'] = $this->produto['codigo_produto'] . $numeroSequencial;
        }
    }

//    public function setPastasImagens($pastas)
//    {
//        //dump("folders", $pastas);
//        //die();
//        $this->pastasImagens = $pastas ?? [];
//    }

    public function setPastasImagensProduto($pastas)
    {
        $this->pastasImagensProduto = $pastas ?? [];
    }

    public function setPastasImagensVariacao($payload)
    {
        // payload = ['variacao_id' => 12, 'pastas' => [...]]
        $this->pastasImagensVariacoes[$payload['variacao_id']] = $payload['pastas'] ?? [];
    }


    /**
     * Processa as imagens enviadas via FilePond e associa ao produto ou variação.
     *
     * @param array $pastasImagens
     * @param string $tipo 'produto' ou 'variacao'
     * @param int $id id do produto pai ou da variação
     */
    public function salvarImagens(array $pastasImagens, string $tipo, int $id): void
    {
        foreach ($pastasImagens as $folder) {
            $temporaryFile = TemporaryFile::where('folder', $folder)->first();

            if ($temporaryFile && Storage::disk('public')->exists('tmp/' . $folder . '/' . $temporaryFile->file)) {
                $file = $temporaryFile->file;
                $pathTemp = 'tmp/' . $folder . '/' . $file;

                // Definir destino e relacionamentos
                if ($tipo === 'produto') {
                    $pathFinal = 'product/' . $id . '/' . $file;
                    $produtoId = $id;
                    $variacaoId = null;
                } else {
                    $pathFinal = 'produtos/' . $id . '/' . $file;
                    $produtoId = null;
                    $variacaoId = $id;
                }

                // Cria diretório destino
                Storage::disk('public')->makeDirectory(dirname($pathFinal));

                // Move arquivo
                Storage::disk('public')->move($pathTemp, $pathFinal);

                // Salva no banco
                ProdutoImagem::create([
                    'produto_id' => $produtoId,
                    'produto_variacao_id' => $variacaoId,
                    'path' => $pathFinal,
                ]);

                // Remove temporário
                Storage::disk('public')->deleteDirectory('tmp/' . $folder);
                $temporaryFile->delete();
            }
        }
    }
}
