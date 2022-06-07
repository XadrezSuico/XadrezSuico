<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Artisan;

class InstallController extends Controller
{
    public function migrate(){
        if(env("__INSTALL_FUNCTIONS",false)){
            echo Artisan::call('migrate');
        }else{
            echo 0;
        }
    }
}
