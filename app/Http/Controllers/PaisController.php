<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }

    
}
