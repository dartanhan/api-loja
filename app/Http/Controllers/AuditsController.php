<?php

namespace App\Http\Controllers;

use App\Http\Models\Audit;
use App\Http\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditsController extends Controller
{
   
    public function index(){

        $user_data = Usuario::where("user_id",auth()->user()->id)->first();
      
         $audits = Audit::
                  leftJoin('loja_produtos_variacao as va', 'audits.auditable_id','=', 'va.id' )
                  ->leftJoin('loja_produtos_new as pn', 'va.products_id','=', 'pn.id' ) 
                  ->leftJoin('users', 'audits.user_id','=', 'users.id' )
                  ->select(
                    'users.name',
                    'audits.event',
                    DB::raw("CONCAT(pn.descricao, ' - ', va.variacao) AS variacao"),
                    'audits.old_values',
                    'audits.new_values',
                    'audits.updated_at')
                 ->where('audits.auditable_type', 'App\Http\Models\ProdutoVariation')
                 ->get();

        return view('admin.audit', compact('user_data','audits'));

    }
}