<?php

namespace App\Http\Controllers;

use App\Http\Models\Ncm;
use App\Http\Models\Usuario;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class NcmController extends Controller
{
    protected $request, $ncm;

    public function __construct(Request $request, Ncm $ncm ){
        $this->request = $request;
        $this->ncm = $ncm;
    }

    public function index()
    {
        if(Auth::check() === true){
            $ncms = $this->ncm->orderBy('numero', 'ASC')->get();
            $user_data = Usuario::where("user_id",auth()->user()->id)->first();
            return view('admin.ncm', compact('ncms','user_data'));
        }
        return redirect()->route('admin.login');
    }

    public function create()
    {
        try {
            $ncms = $this->ncm::get();
            if(empty($ncms))
                return Response()->json(array('data'=>''));
        } catch (Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return Response()->json($ncms);
    }

    public function store(Request $request)
    {
        try {
            $data = $this->request->all();
            $validator = Validator::make($data, [
                'numero' => 'required|unique:'.$this->ncm->table.'|max:20',
                'nome' => 'required|max:255',
            ],[
                'numero.max'=> 'Valor numero deve ser menos que 20 caracteres!',
                'numero.required'=> 'Valor numero é obrigatório!',
                'numero.unique'  => 'Numero já cadastrado!',
                'nome.required' => 'O nome é obrigatório!',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return Response::json(array('success' => false,'message' => $error), 400);
            }

            $this->ncm->create($data);
            
        } catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage()), 500);
        }
        return Response::json(array('success' => true, 'message' => 'NCM criado com sucesso!'), 201);
    }

    public function update(Request $request)
    {
        try {
            $this->ncm = $this->ncm::find($this->request->input('id'));
            $this->ncm->numero = $this->request->input("numero");
            $this->ncm->nome = $this->request->input("nome");
            $this->ncm->save();

            return Response::json(array('success' => true, 'message' => 'Dados atualizados com sucesso!'), 200);
        }catch (Throwable $e) {
            return Response::json(['success' => false, 'message' => $e->getMessage() ], 500);
        }
    }

    public function destroy($id)
    {
        try{
            $ncm = $this->ncm::find($id)->delete();
            if(!$ncm)
                return Response::json(array("success" => false, "message" => utf8_encode("NCM não localizado para deleção com o id: [ {$id} ]")), 400);

        }catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1451') {
                return Response::json(array('success' => false, 'message' => 'NCM não pode ser removido, ele está sendo usado no sistema!'), 400);
            }
        }catch (Throwable $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage() ), 500);
        }
        return Response::json(array("success" => true, "message" => "NCM deletado com sucesso!"), 200);
    }
    
    public function search(Request $request)
    {
        $term = $request->input('q');
        $ncms = $this->ncm::where('numero', 'like', "%{$term}%")
                    ->orWhere('nome', 'like', "%{$term}%")
                    ->limit(20)
                    ->get();
        
        $results = [];
        foreach($ncms as $ncm) {
            $results[] = [
                'id' => $ncm->numero, // Envimos o número para o campo NCM no frontend
                'text' => $ncm->numero . ' - ' . $ncm->nome
            ];
        }
        return Response::json($results);
    }
}
