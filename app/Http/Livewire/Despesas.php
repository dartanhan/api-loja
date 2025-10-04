<?php


namespace App\Http\Livewire;


use App\Helpers\LivewireHelper;
use App\Traits\ComumTrait;
use Illuminate\Support\Carbon;
use Livewire\Component;
use App\Http\Models\Despesa;
use Livewire\WithPagination;

class Despesas extends Component
{
    use WithPagination;
    use ComumTrait;

    public $valor, $descricao, $data;
    public $editandoId = null;
    protected $listeners = ['excluirDespesa' => 'excluir'];


    protected $rules = [
        'valor' => 'required|string|max:25',
        'descricao' => 'required|string|max:255',
        'data' => 'required|date',
    ];

    protected $messages = [
        'valor.required' => 'O valor é obrigatório.',
        'valor.max' => 'O valor deve ser maximo com 25 caracteres.',
        'descricao.required' => 'A descrição é obrigatória.',
        'data.required' => 'A data é obrigatória.',
        'data.date' => 'Informe uma data válida.',
    ];

    public function salvar()
    {
        $this->validate();

        Despesa::create([
            'valor' => LivewireHelper::formatCurrencyToBD($this->valor, $this->NumberFormatter()),
            'descricao' => $this->descricao,
            'data' => Carbon::parse($this->data),
        ]);

        $this->reset(['valor', 'descricao', 'data']);

        $this->dispatchBrowserEvent('toast:success', [
            'message' => 'Despesa cadastrada com sucesso!',
            'color' => 'white',
            'background' => 'green'
        ]);

        /*$this->dispatchBrowserEvent('livewire:event', [
            'type'    => 'alert',
            'icon'    => 'success',
            'message' => 'Despesa cadastrada com sucesso!'
        ]);*/

    }

    public function editar($id)
    {
        $despesa = Despesa::findOrFail($id);
        $this->editandoId = $id;
        $this->valor = $despesa->valor;
        $this->descricao = $despesa->descricao;
        $this->data = $despesa->data->format('Y-m-d');
    }

    public function atualizar()
    {
        $this->validate();

        $despesa = Despesa::findOrFail($this->editandoId);
        $despesa->update([
            'valor' => $this->valor,
            'descricao' => $this->descricao,
            'data' => Carbon::parse($this->data),
        ]);

        $this->reset(['valor', 'descricao', 'data', 'editandoId']);

        $this->dispatchBrowserEvent('toast:success', [
            'message' => 'Despesa atualizada com sucesso!',
            'color' => 'white',
            'background' => 'green'
        ]);
    }

    public function cancelarEdicao()
    {
        $this->reset(['valor', 'descricao', 'data', 'editandoId']);
    }

    public function excluir($id)
    {
        Despesa::findOrFail($id)->delete();

        $this->dispatchBrowserEvent('toast:success', [
            'message' => 'Despesa excluída com sucesso!',
            'color' => 'white',
            'background' => 'green'
        ]);


    }


    public function render()
    {
        return view('livewire.despesa', [
            'despesas' => Despesa::orderBy('data', 'desc')->paginate(5)
        ]);
    }

}
