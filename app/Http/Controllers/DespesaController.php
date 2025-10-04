<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DespesaController extends Controller
{
    public function index()
    {
        if (Auth::check() === true) {
            return view('admin.despesa');
        }
    }
}
