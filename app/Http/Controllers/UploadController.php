<?php

namespace App\Http\Controllers;

use App\Http\Models\TemporaryFile;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

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

    public function tmpUpload()
    {
        if ($this->request->hasFile('image')) {
            $files = $this->request->file('image');

            foreach ((array)$files as $file) {
                $nome_unico = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $folder = uniqid("temporary", true);

                $image = Image::make($file);
                $image->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $temp_file = $image->encode('jpeg');

                Storage::put("tmp/{$folder}/{$nome_unico}", $temp_file->encoded, 'public');

                $this->temporaryFile->create([
                    'folder' => $folder,
                    'file'   => $nome_unico,
                ]);

                return response($folder);
            }
        }

        return response('Nenhum arquivo enviado', 400);
    }



    public function tmpDelete(){
        $temp_file = TemporaryFile::where('folder',$this->request->getContent())->first();

        if($temp_file){
            Storage::deleteDirectory('tmp/'.$temp_file->folder);
            $temp_file->delete();
            return response('');
        }
    }

    public function tmpUploadVariacao(){

        if ($this->request->hasFile('image')) {
            $image = $this->request->file('image');

            $destinoFile = 'produtos';

            $nome_unico = Str::uuid() . '.' . $image->getClientOriginalExtension();

            $folder = uniqid("product", true);

            // Redimensionar imagem
            $image = Image::make($image);
            $image->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $temp_file = $image->encode('jpeg');

            // Armazenar imagem redimensionada
            Storage::put($destinoFile.'/tmp/'.$folder.'/'.$nome_unico, $temp_file->encoded, 'public');

            $this->temporaryFile->folder = $folder;
            $this->temporaryFile->file =  $nome_unico;

            $this->temporaryFile->save();

            return $folder;
        }
        return '';
    }

    public function tmpDeleteVariacao(){
        $temp_file = TemporaryFile::where('folder',$this->request->getContent())->first();

        if($temp_file){
            $destinoFile = 'product';

            Storage::deleteDirectory($destinoFile.'/tmp/'.$temp_file->folder);
            $temp_file->delete();
            return response('');
        }

    }

}
