<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Artisan;

class InstallController extends Controller
{
    public function migrate(){
        if(env("__INSTALL_FUNCTIONS",false)){
            Artisan::call('migrate');
            echo Artisan::output();
        }else{
            echo "n";
        }
    }



    public function vinculos_pre_vinculate(){
        if(env("__INSTALL_FUNCTIONS",false)){
            Artisan::call('fexpar:vinculos --pre-vinculate');
            echo Artisan::output();
        }else{
            echo "n";
        }
    }

    public function vinculos_vinculate(){
        if(env("__INSTALL_FUNCTIONS",false)){
            Artisan::call('fexpar:vinculos --vinculate');
            echo Artisan::output();
        }else{
            echo "n";
        }
    }
}
