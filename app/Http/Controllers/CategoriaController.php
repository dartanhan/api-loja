<?php

namespace App\Http\Controllers;

use App\Http\Models\Categoria;
use App\Http\Models\TemporaryFile;
use App\Http\Models\Usuario;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class CategoriaController extends Controller
{

    protected $request,$categoria;

    public function __construct(Request $request, Categoria $categoria){

        $this->request = $request;
        $this->categoria = $categoria;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $user_data = Usuario::where("user_id",auth()->user()->id)->first();
        return view('admin.categoria',compact('user_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        try {
            $categorias = $this->categoria::all();

            $resultado = $categorias->map(function ($categoria) {
                return [
                    'id' => $categoria->id,
                    'nome' => $categoria->nome,
                    'slug' => $categoria->slug,
                    'imagem' => $categoria->imagem,
                    'quantidade' => $categoria->quantidade,
                    'status' => $categoria->status == 1 ? 'ATIVO' : 'INATIVO',
                    'created_at' => $categoria->created_at->format('d/m/Y H:i:s'),
                    'updated_at' => $categoria->updated_at->format('d/m/Y H:i:s'),
                ];
            });

            return response()->json($resultado->isNotEmpty() ? $resultado : ['data' => '']);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store()
    {

        try{
            $validator  = Validator::make($this->request->all(),[
                'nome' => 'required|unique:'.$this->categoria->table.'|max:155',
                'status' => 'required|max:1',
                'image' => 'required'
            ],[
                'nome.unique'  => 'Categoria já cadastrado!',
                'nome.required'=> 'Categoria é obrigatório!',
                'nome.max'=> 'Categoria deve ser menos que 155 caracteres!',
                'status.required'  => 'Status é obrigatório!',
                'status.max'  => 'Status deve ser 1 caracter!',
                'image.required'=> 'Imagem é obrigatório!',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }


            $cat = Categoria::create([
                'nome' => $this->request->input('nome'),
                'slug' => $this->request->input('slug'),
                'status' =>  $this->request->input('status')
            ]);

            $temp_file = TemporaryFile::where('folder',$this->request->image)->first();

            $destinationPath = 'categorias/'.$cat->id.'/'.$temp_file->file;

            //if (app()->environment('local')) {
            //    $destinationPath = 'public/' . $destinationPath;
           // }

            if($temp_file){
                Storage::copy('tmp/'.$temp_file->folder.'/'.$temp_file->file, $destinationPath);

                Storage::deleteDirectory('tmp/'.$temp_file->folder);
                $temp_file->delete();
            }

            $this->categoria->where('id', $cat->id)
                ->update(['imagem' => $temp_file->file]);

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage() ), 500);
        }
        return Response::json(array('success' => true, 'message' => 'Categoria cadastrada com sucesso!!'), 201);
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

        try {

            $validator = Validator::make($this->request->all(), [
                'nome' => 'required|max:155|unique:' . $this->categoria->table . ',nome,' . $this->request->input('id'),
                'status' => 'required|max:1'
            ],[
                'nome.unique'  => 'Categoria já cadastrado!',
                'nome.required'=> 'Categoria é obrigatório!',
                'nome.max'=> 'Categoria deve ser menos que 155 caracteres!',
                'status.required'  => 'Status é obrigatório!',
                'status.max'  => 'Status deve ser 1 caracter!']);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }
            $this->categoria = $this->categoria::find($this->request->input('id'));

            if($this->request->image){

                $temp_file = TemporaryFile::where('folder',$this->request->image)->first();

                if($temp_file){
                    $destinationPath = 'categorias/'.$this->categoria->id;

                   // if (app()->environment('local')) {
                   //     $destinationPath = 'public/' . $destinationPath;
                  //  }

                    Storage::deleteDirectory($destinationPath);

                     Storage::copy('tmp/'.$temp_file->folder.'/'.$temp_file->file, $destinationPath."/".$temp_file->file);
                     // Storage::copy('tmp/'.$temp_file->folder.'/'.$temp_file->file,'categorias/'.$this->request->input('id')."/".$temp_file->file);

                     Storage::deleteDirectory('tmp/'.$temp_file->folder);
                     $temp_file->delete();
                }
                $this->categoria->imagem = $temp_file->file;
            }else{
                return Response::json(array('success' => false, 'message' => 'Informe a imagem de atualização!!'), 400);
            }

            $this->categoria->nome = $this->request->input('nome');
            $this->categoria->slug = $this->request->input('slug');
            $this->categoria->status = $this->request->input('status');

            $this->categoria->save();

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return Response::json(array('success' => false, 'message' => 'Categoria já cadastrada!!'), 400);
            }
        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'CategoriaController -> update()' . $e ), 500);
        }
        return Response::json(array('success' => true, 'message' => 'Categoria atualizada com sucesso!'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        try{
            $category = $this->categoria::find($id)->delete();

            if(!Storage::deleteDirectory('categorias/'.$id)){
                return Response::json(array("success" => false, "message" => "Não foi possivel deletar a imagem da categoria id: [ {$id} ]"), 400);
            }

            if(!$category)
                return Response::json(array("success" => false, "message" => "Categoria não localizado para deleção com o id: [ {$id} ]"), 400);

        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'Categoria não pode ser removido, ele está sendo usado no sistema!'), 400);
            }

        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => 'CategoriaController -> delete()' . $e ), 500);
        }
        return Response::json(array("success" => true, "message" => "Categoria deletada com sucesso!"),200);
    }
}
