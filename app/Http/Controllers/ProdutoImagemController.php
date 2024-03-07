<?php

namespace App\Http\Controllers;


use App\Http\Models\Produto;
use App\Http\Models\ProdutoImagem;
use App\Http\Models\TemporaryFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Intervention\Image\Facades\Image;
use Throwable;


class ProdutoImagemController extends Controller
{
    protected Request $request;
    protected ProdutoImagem $produtoImagem;
    protected Produto $produto;

    public function __construct(Request $request, ProdutoImagem $produtoImagem, Produto $produto){
        $this->request = $request;
        $this->produtoImagem = $produtoImagem;
        $this->produto = $produto;
    }

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store()
    {
        // dd($this->request->all());
        //dd($this->request->allFiles()['images'][0]);

        try {
            $validator = Validator::make($this->request->all(), [
                'images' => 'required|image|mimes:gif,jpg,jpeg,png|max:2048',
            ],[
                'images.required'=> 'Informe a Imagem do Produto!',
                'images.max'=> 'Imagem deve ter no máximo 2 Megas!'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

                $file = $this->request->allFiles()['images'];

                $image_name =  $file->hashName();
                $path = storage_path('app/public/produtos/'.$this->request->products_variation_id) ;

                File::makeDirectory($path , 0775, true, true);
                $image_resize = Image::make($file->path());
                $image_resize->resize(420,240)->save($path .'/'.$image_name);

                $productsImages = new ProdutoImagem();
                $productsImages->produto_variacao_id = $this->request->products_variation_id;
                //$productsImages->path = $file->store('produtos/' . $this->request->products_variation_id);
                $productsImages->path = 'produtos/' . $this->request->products_variation_id .'/'. $image_name;
                $productsImages->save();
                unset($productsImages);

                //Storage::disk('public')->put('produtos/'.$this->request->products_variation_id ,  $file, 'public');

               //$destinationPath = public_path('/produtos');
               /* $total = count($this->request->allFiles()['images']);
                if ($total > 0) {
                   for ($j = 0; $j < $total; $j++) {
                       $file = $this->request->allFiles()['images'][$j];

                       /*$image_name =  $file->hashName(); // time().'.'.$file->extension();

                       $image_resize = Image::make($file->path());
                       $image_resize->resize(250,250)->save($destinationPath.'/' . $this->request->products_variation_id .'/'.$image_name);
                      // $image_resize->save(public_path('storage/produtos/'.$this->request->products_variation_id .'/' .$image_name));
                    */
                     /*   $productsImages = new ProdutoImagem();
                        $productsImages->produto_variacao_id = $this->request->products_variation_id;
                        $productsImages->path = $file->store('produtos/' . $this->request->products_variation_id);
                      //  $productsImages->path = 'produtos/' . $this->request->products_variation_id .'/'. $image_name;
                        $productsImages->save();
                        unset($this->productsImages);
                    }
                }*/
        }catch (Throwable $e){
            return Response::json(array('success' => false, 'message' => $e->getMessage()), 500);
        }
        return Response::json(array('success' => true, 'message' => "Imagem cadastrada com sucesso!"), 201);
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return JsonResponse
     */
    public function update()
    {
       // dd($this->request->input("product_id"));
     //   dd($this->request->all());
        try {

        $temp_file = TemporaryFile::where('folder',$this->request->image)->first();

        if($temp_file){
            Storage::copy('categorias/tmp/'.$temp_file->folder.'/'.$temp_file->file,'product/'.$this->request->input("product_id")."/".$temp_file->file);

            Storage::deleteDirectory('categorias/tmp/'.$temp_file->folder);
            $temp_file->delete();
        }

            $this->produto->where('id', $this->request->input("product_id"))->update(['imagem' => $temp_file->file]);

    }catch (Throwable $e) {
        return Response::json(array('success' => false, 'message' => $e->getMessage() ), 500);
    }
        return Response::json(array('success' => true, 'message' => 'Imagem cadastrada com sucesso!!'), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function destroy()
    {
        try {
            $id = $this->request->input("id");

            $image = $this->produtoImagem::find($id);
           // dd($image);
            if(!$image){
                return Response::json(array("success" => false, "message" => utf8_encode("Imagem não localizado id: [ {$id} ]")), 400);
            }else {

                $path = public_path('../storage/' . $image->path);
                  //dd($path);
                if (!File::exists($path)) {
                   // dd($path);
                    //Remove a imagem
                    File::delete(public_path('storage/' .  $image->path));
                    $this->produtoImagem::destroy($id);

                    //Verifica quantas imagens existem
                    $count = $this->produtoImagem::where('produto_variacao_id', '=', $id)->count();

                    // se tiver 0 apaga o diretório também
                    if ($count === 0) {
                        if ($id < 10)
                            $id = "0" . $image->produto_variacao_id;

                        //dd($path.'/'.$id );
                        File::deleteDirectory('storage/app/public/produtos/' . $id);
                    }
                }
            }
         }catch (Throwable $e){
            return Response::json(array('success' => false, 'message' => $e->getMessage()), 500);
        }
        return Response::json(array('success' => true, 'message' => "Imagem removida com sucesso!"), 200);
    }
}
