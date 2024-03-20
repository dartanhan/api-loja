<?php

namespace App\Http\Controllers;

use App\Http\Models\Audit;
use App\Http\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AuditsController extends Controller
{
   
    public function index(){

        $user_data = Usuario::where("user_id",auth()->user()->id)->first();
      
         $auditsUpdate = Audit::
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
                 ->where('audits.event', 'updated')
                 ->get();

          $auditsCreate = Audit::
                 leftJoin('loja_produtos_variacao as va', 'audits.auditable_id','=', 'va.id' )
                 ->leftJoin('loja_produtos_new as pn', 'va.products_id','=', 'pn.id' ) 
                 ->leftJoin('users', 'audits.user_id','=', 'users.id' )
                 ->select(
                   'users.name',
                   'audits.event',
                   DB::raw("CONCAT(pn.descricao, ' - ', va.variacao) AS variacao"),
                   'audits.old_values',
                   'audits.new_values',
                   'pn.updated_at')
                ->where('audits.auditable_type', 'App\Http\Models\VendasProdutos')
                ->where('audits.event', 'created')
                ->get();
                

        return view('admin.audit', compact('user_data','auditsUpdate','auditsCreate'));

    }

    public function datatableAuditUpdate(Request $request){

      $auditsUpdate = Audit::
                  leftJoin('loja_produtos_variacao as va', 'audits.auditable_id','=', 'va.id' )
                  ->leftJoin('loja_produtos_new as pn', 'va.products_id','=', 'pn.id' ) 
                  ->leftJoin('users', 'audits.user_id','=', 'users.id' )
                  ->select(
                    'users.name as usuario',
                    'audits.event as evento',
                    DB::raw("CONCAT(pn.descricao, ' - ', va.variacao) AS variacao"),
                    'audits.old_values',
                    'audits.new_values',
                    'audits.updated_at')
                 ->where('audits.auditable_type', 'App\Http\Models\ProdutoVariation')
                 ->where('audits.event', 'updated');

                // return  DataTables::of($auditsUpdate)->make(true);
                // return DataTables::of($auditsUpdate)
                // ->setFilteredRecords(100)
                // ->toJson();

                return DataTables::of($auditsUpdate)->toJson();
                 
    }
}