<?php

namespace App\Imports;

use App\Http\Models\ProdutoVariation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collections
     */
    public function collection(Collection $collections)
    {
        //dd($collections);
        foreach ($collections as $row)
        {
            $product = ProdutoVariation::where('subcodigo',$row['codigo_produto'])->first();
          //  dd($product);

            ProdutoVariation::where('id', $product->id)->update([
                'variacao' => $row['variacao'],
                'valor_varejo' => $row['valor_varejo'],
                'valor_atacado' => $row['valor_atacado'],
                'valor_produto' => $row['valor_produto'],
                'quantidade_minima' => $row['quantidade_minima'],
                'quantidade' => $row['quantidade'] + $product->quantidade
            ]);
        }
    }
}
