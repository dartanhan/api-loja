<?php


namespace App\Http\Livewire;


use Livewire\Component;

class ProdutoPreviewImage extends Component
{

    public function render()
    {
        return view('livewire.produto-preview-image')->layout('layouts.layout');
    }

}
