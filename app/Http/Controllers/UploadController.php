<?php

namespace App\Http\Controllers;

use App\Http\Models\TemporaryFile;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/***
 * Classe responsável pelo upload temporário das imagens do sistema
 */
class UploadController extends Controller
{

    protected $request,$temporaryFile;

    public function __construct(Request $request, TemporaryFile $temporaryFile){

        $this->request = $request;
        $this->temporaryFile = $temporaryFile;
    }
    public function tmpUpload(){

        if($this->request->hasFile('image')){
            $image = $this->request->file('image');
            $destinoFile = $this->request->input("productId") !== "" ? 'product' : 'produtos';

            
            $nome_unico = Str::uuid() . '.' . $image->getClientOriginalExtension();
           // $file_name = $image->getClientOriginalName();
            $folder = uniqid($destinoFile,true);
            $image->storeAs($destinoFile.'/tmp/'.$folder,$nome_unico,'public');

            $this->temporaryFile->folder = $folder;
            $this->temporaryFile->file =  $nome_unico;

           // dd($this->temporaryFile);
            $this->temporaryFile->save();

            return $folder;
        }
        return '';
    }

    public function tmpDelete(){
        $temp_file = TemporaryFile::where('folder',$this->request->getContent())->first();

        if($temp_file){
            $destinoFile = $this->request->input('destinoFile');
            Storage::deleteDirectory($destinoFile.'/tmp/'.$temp_file->folder);
            $temp_file->delete();
            return response('');
        }

    }
}
