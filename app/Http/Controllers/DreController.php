<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DreController extends Controller
{
    public function index()
    {
        if (Auth::check() === true) {
            return view('admin.dre.dre');
        }
    }
}
