<?php

namespace App\Http\Controllers;

use App\Http\Models\Audit;
use App\Http\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class AuditsController extends Controller
{
   private $user_data;
   protected $user;

    public function __construct()
    {
      $this->middleware(function ($request, $next) {
        $this->user = FacadesAuth::user(); // Recupera o usuário autenticado
        $this->user_data = Usuario::where("user_id",$this->user->id)->first();
        return $next($request);
      });
    }
    public function index(){

       // $user_data = Usuario::where("user_id",auth()->user()->id)->first();
        // Calcula a data de dois meses atrás
        $twoMonthsAgo = Carbon::now()->subMonths(1);

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
                 ->where('audits.created_at', '>=', $twoMonthsAgo)
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
                ->where('audits.created_at', '>=', $twoMonthsAgo)
                ->get();


        return view('admin.audit', compact($this->user_data,'auditsUpdate','auditsCreate'));

    }

    public function datatableAuditUpdate(Request $request){
        // Calcula a data de dois meses atrás
        $twoMonthsAgo = Carbon::now()->subMonths(1);

        if($request->ajax()){
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
            ->where('audits.event', 'updated')
            ->where('audits.created_at', '>=', $twoMonthsAgo);

        return DataTables::of($auditsUpdate)->toJson();
        }
    }
}
