<?php

namespace App\Http\Controllers;

use App\Http\Models\TemporaryFile;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{

    protected $request,$temporaryFile;

    public function __construct(Request $request, TemporaryFile $temporaryFile){

        $this->request = $request;
        $this->temporaryFile = $temporaryFile;
    }
    public function tmpUpload(){

        if($this->request->hasFile('image')){
            $image = $this->request ->file('image');
            $nome_unico = Str::uuid() . '.' . $image->getClientOriginalExtension();
           // $file_name = $image->getClientOriginalName();
            $folder = uniqid('categorias',true);
            $image->storeAs('categorias/tmp/'.$folder,$nome_unico,'public');

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
            Storage::deleteDirectory('categorias/tmp/'.$temp_file->folder);
            $temp_file->delete();
            return response('');
        }

    }
}
