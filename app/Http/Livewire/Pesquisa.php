<?php

namespace App\Http\Livewire;

use Illuminate\Support\Carbon;
use Livewire\Component;

class Pesquisa extends Component
{
    public $data_range;

    public function mount()
    {
        $inicio = Carbon::now()->format('d/m/Y');
        $fim = Carbon::now()->format('d/m/Y');

        $this->data_range = "$inicio - $fim";

        // Opcional: emitir para o pai logo ao montar
        $this->emitUp('periodoAtualizado', $this->data_range);
    }


    public function updatedDataRange()
    {
        if ($this->data_range && str_contains($this->data_range, ' - ')) {
            $this->emitUp('periodoAtualizado', $this->data_range);
        }
    }


    public function render()
    {
        return view('livewire.pesquisa');
    }
}
