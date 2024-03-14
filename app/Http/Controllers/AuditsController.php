<?php

namespace App\Http\Controllers;

use App\Http\Models\Usuario;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditsController extends Controller
{
   
    public function index(){

        $user_data = Usuario::where("user_id",auth()->user()->id)->first();
        
        $audits = Audit::where('auditable_type', 'App\Http\Models\ProdutoVariation')->get();

      //  dd($audits);
        // $audits = Audit::with(['user', 'productNew'])
        //         ->where('auditable_type', 'App\Models\ProdutoVariation')
        //         ->get();


        return view('admin.audit', compact('user_data','audits'));

    }
}