<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * Exibe a página sobre o Leia Livre
     */
    public function index()
    {
        return view('about.index');
    }
}

