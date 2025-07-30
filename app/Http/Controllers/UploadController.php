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

        //if ($this->request->hasFile('image')) {
          //  $files = $this->request->file('image');
        $files = collect($this->request->allFiles())->flatten(99);

            if ($files->isEmpty()) {
                return response('Nenhum arquivo enviado', 400);
            }

            foreach ($files as $file) {
                $nome_unico = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $folder = uniqid("temporary", true);

                $image = Image::make($file);
                $image->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $temp_file = $image->encode('jpeg');

                //Storage::put("tmp/{$folder}/{$nome_unico}", $temp_file->encoded, 'public')
                Storage::disk('public')->put("tmp/{$folder}/{$nome_unico}", $temp_file->encoded);


                $this->temporaryFile->create([
                    'folder' => $folder,
                    'file'   => $nome_unico,
                    'variacao_id' => $this->request->input('variacao_id') ?? null // se quiser registrar no temp também
                ]);

                return response($folder);
            }
    }


    public function tmpDelete(Request $request){

        $folder = $request->input('folder');

        if ($folder) {
            $temp_file = TemporaryFile::where('folder', $folder)->first();

            if ($temp_file) {
                //Storage::deleteDirectory('tmp/'.$temp_file->folder);
                Storage::disk('public')->deleteDirectory('tmp/' . $temp_file->folder);
                $temp_file->delete();
                return response('');
            }
        }
        return response()->json(['error' => 'Arquivo não encontrado.'], 404);
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
            //Storage::put($destinoFile.'/tmp/'.$folder.'/'.$nome_unico, $temp_file->encoded, 'public');
            Storage::disk('public')->put("tmp/{$folder}/{$nome_unico}", $temp_file->encoded);

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
