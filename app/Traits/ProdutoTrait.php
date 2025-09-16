<?php

namespace App\Traits;

use App\Http\Models\Produto;
use App\Http\Models\ProdutoVariation;
use NumberFormatter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\TemporaryFile;
use App\Http\Models\ProdutoImagem;
use Livewire\Livewire;

trait ProdutoTrait
{
    /**
     * Carrega os dados na tela de edição, as variações
     */
    private function carregdaDadosVariacao($produto)
    {

        if (!empty($produto['variacoes'])) {
            return $produto['variacoes']->map(fn($v) => [
                'id' => $v->id,
                'subcodigo' => $v->subcodigo,
                'variacao' => $v->variacao,
                'quantidade' => $v->quantidade,
                'valor_varejo' => number_format($v->valor_varejo, 2, ',', '.'),
                'valor_produto' => number_format($v->valor_produto, 2, ',', '.'),
                'gtin' => $v->gtin,
                'estoque' => $v->estoque,
                'quantidade_minima' => $v->quantidade_minima,
                'percentage' => number_format($v->percentage, 2, ',', '.'),
                'validade' => Carbon::parse($v->validade)->format('d/m/Y'),
                'fornecedor_id' => $v->fornecedor,
                'status' => $v->status,
                'images' => $v->images->map(fn($i) => [
                    'id' => $i->id,
                    'path' => $i->path
                ])->toArray(),
            ])->toArray();
        }
    }

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

                $this->dispatchBrowserEvent('status-alterado', ['text' => 'Produto e variações desativados com sucesso.']);
            } else {
                // Apenas ativa direto
                $produto->status = 1;
                $produto->save();
                $this->dispatchBrowserEvent('status-alterado', ['text' => 'Produto ativado com sucesso.']);
            }
        } else {

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

                $this->dispatchBrowserEvent('status-alterado', ['text' => 'Produto e variações desativados.']);
            } else {
                $variacao = ProdutoVariation::findOrFail($data['variacaoId']);
                $variacao->status = 0;
                $variacao->save();
                $this->dispatchBrowserEvent('status-alterado', ['text' => 'Variação desativada com sucesso.']);
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
            $variacao['subcodigo'] = $this->codigoPai . $numeroSequencial;
        }
    }


    /**
     * @param $imagemId
     * @param $isVariacao
     * @param $produtoId
     */
    public function deletarImagem($imagemId, $isVariacao, $produtoId)
    {

        $imagem = ProdutoImagem::find($imagemId);

        if ($isVariacao) {
            if ($imagem && Storage::disk('public')->exists($imagem->path)) {
                // Apaga do storage
                Storage::disk('public')->delete($imagem->path);

                // Pega o diretório da imagem
                $diretorio = dirname($imagem->path);

                // Remove do banco
                $imagem->delete();

                // Se o diretório ficou vazio, remove também
                if (empty(Storage::disk('public')->files($diretorio))) {
                    Storage::disk('public')->deleteDirectory($diretorio);

                    $produto = Produto::with('variacoes.images', 'images')->findOrFail($produtoId); //trás o PAI e sua relações
                    //carrego os dados das variações do produto para blade
                    $this->variacoes = $this->carregdaDadosVariacao($produto);
                    //aciona o componente para atualizar as imagens
                    $this->emitTo('produto-variacoes-form', 'imagemAtualizada', $this->variacoes);

                }
            }

            // Recarrega imagens da variação
//            if ($variacaoId) {
//                $this->imagens = ProdutoVariation::with('images')->findOrFail($variacaoId)->images;
//            }
            // $this->produto = Produto::with('variacoes.images','images')->findOrFail($produtoId); //trás o PAI e sua relações
            // Notifica o FilepondUpload


        } else {

            //$imagemPath = "product/{$imagem->produto_id}/{$imagem->path}";
            $diretorio = "product/{$imagem->produto_id}";

            if (Storage::disk('public')->exists($diretorio)) {
                Storage::disk('public')->deleteDirectory($diretorio);
            }

            // Remove do banco
            $imagem->delete();

            // Atualiza lista de imagens do produto/variação
            $this->imagensExistentes = $this->produto->images->toArray(); // ou variação

            // Notifica o FilepondUpload
            $this->emitTo('filepond-upload', 'imagemAtualizada', $this->imagensExistentes);
        }

        // 🔹 Dispara eventos para atualizar UI
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

    /**
     * Processa as imagens enviadas via FilePond e associa ao produto ou variação.
     *
     * @param array $pastasImagens
     * @param string $tipo 'produto' ou 'variacao'
     * @param int $id id do produto pai ou da variação
     */
    /* public function salvarImagens(array $pastasImagens, string $tipo, int $id): void
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
                     'path' => $tipo === 'produto' ? $file : $pathFinal,
                 ]);

                 // Remove temporário
                 Storage::disk('public')->deleteDirectory('tmp/' . $folder);
                 $temporaryFile->delete();
             }

         }
     }
 */


    /**
     * @param array $pastasImagens
     * @param string $destino
     * @param int $id
     * @param ProdutoImagem $imagem
     */
    public function salvarImagemV2(string $arquivo, string $destino, int $id): void
    {
        $pathTemp = 'livewire-tmp/' . $arquivo;

        // Ignora se o arquivo temporário não existir
        if (!Storage::disk('public')->exists($pathTemp)) {
            return;
        }

        if ($destino === 'produto') {
            // Produto PAI (somente 1 imagem)
            $pathFinal = "product/{$id}/{$arquivo}";

            Storage::disk('public')->makeDirectory(dirname($pathFinal));
            Storage::disk('public')->move($pathTemp, $pathFinal);

            // Atualiza ou cria imagem do produto pai
            ProdutoImagem::updateOrCreate(
                ['produto_id' => $id, 'produto_variacao_id' => null],
                ['path' => $arquivo]
            );
        } else {
            // VARIAÇÃO (pode ter várias imagens)
            $pathFinal = "produtos/{$id}/{$arquivo}";

            Storage::disk('public')->makeDirectory(dirname($pathFinal));
            Storage::disk('public')->move($pathTemp, $pathFinal);

            // Cria uma nova imagem para a variação
            ProdutoImagem::create([
                'produto_id' => null,
                'produto_variacao_id' => $id,
                'path' => $pathFinal
            ]);
        }

        // Remove arquivo temporário
        Storage::disk('public')->delete($pathTemp);
    }




    /**
     * @param array $variacoes
     * quando for salvar tanto no create quanto no edit, seta os dados das variações para o PAI
     */
    public function setVariacoes(array $variacoes)
    {
        $this->variacoes = $variacoes; // agora pai tem as variações
    }

    /**
     * seta imagem temporaria para os componentes PAI
     * @param $images
     */
    public function setImagens($images)
    {
        $this->images = $images; // agora pai tem as imagens
    }
    /**
     * Retorna o NumberFormatter
    */
    public function NumberFormatter()
    {
        return  new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
    }


    /**
     * Adiciona imagem no array unificado
     * @param array $payload
     */
    public function addImagem(array $payload): void
    {
        // payload esperado: ['context' => 'produto'|'variacao', 'file' => string, 'variacaoKey' => int|null]

        if ($payload['context'] === 'produto') {
            // Produto só pode ter 1 imagem → substitui
            $this->images = collect($this->images)
                ->reject(fn($img) => $img['context'] === 'produto')
                ->push([
                    'context' => 'produto',
                    'file'    => $payload['file'], // sempre string
                ])
                ->values()
                ->toArray();
        }

        if ($payload['context'] === 'variacao') {
            $this->images[] = [
                'context'     => 'variacao',
                'variacaoKey' => $payload['variacaoKey'],
                'file'        => $payload['file'], // sempre string
            ];
        }
        $this->emitUp('imagensAtualizadas', $this->images);
    }


    /**
     * Remove imagem do array unificado
     * @param string $file
     */
    public function removeImagem(string $file): void
    {
        $this->images = collect($this->images)
            ->reject(fn($img) => $img['file'] === $file)
            ->values()
            ->toArray();
    }

}
