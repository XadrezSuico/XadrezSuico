<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard (layout v2).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the legacy AdminLTE dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function indexLegacy()
    {
        return view('home-legacy');
    }
}
