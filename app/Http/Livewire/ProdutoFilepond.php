<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ProdutoFilepond extends Component
{
  //  public $modelId;  // ID do produto ou variação (opcional, se precisar vincular depois)

    use WithFileUploads;

 //   public $files = [];

    public function mount($modelId = null)
    {
      //  $this->modelId = $modelId;
    }

//    public function updatedFiles()
//    {
//        foreach ($this->files as $file) {
//            $path = $file->store('produtos/' . $this->modelId);
//            // Salva no DB
//            ProdutoFile::create([
//                'produto_id' => $this->modelId,
//                'filename' => $path,
//            ]);
//        }
//        $this->files = [];
//        $this->emit('filepondUploaded');
//    }

//    public function deleteFile($fileId)
//    {
//        $file = ProdutoFile::find($fileId);
//        if ($file) {
//            Storage::delete($file->filename);
//            $file->delete();
//            $this->emit('fileDeleted', $fileId);
//        }
//    }

    public function render()
    {
        //$uploadedFiles = ProdutoFile::where('produto_id', $this->modelId)->get();
        //return view('livewire.produto-filepond', compact('uploadedFiles'));
        return view('livewire.produto-filepond');
    }

//    public function upload()
//    {
//        $this->validate([
//            'files.*' => 'image|max:2048',
//        ]);
//
//        $paths = [];
//        foreach ($this->files as $file) {
//            $paths[] = $file->store('produtos', 'public');
//        }
//
//        return response()->json(['paths' => $paths]);
//    }
//
//    public function revert($filename)
//    {
//        Storage::disk('public')->delete($filename);
//        return response()->json(['status' => 'ok']);
//    }
//
//    public function render()
//    {
//        return view('livewire.produto-filepond');
//    }
}
