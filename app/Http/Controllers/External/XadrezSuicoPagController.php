<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class XadrezSuicoPagController extends Controller
{
    private static $instance;

    private static $controllers = [];

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function factory($name){
        if(isset(self::$controllers[$name])){
            return self::$controllers[$name];
        }

        switch($name){
            case "category":
            case "categories":
                $name = "category";
                self::$controllers[$name] = new XadrezSuicoPagCategoryController;
                break;
            case "registration":
            case "registrations":
                $name = "registration";
                self::$controllers[$name] = new XadrezSuicoPagRegistrationController;
                break;
            case "notification":
                $name = "notification";
                self::$controllers[$name] = new XadrezSuicoPagNotificationController;
                break;
        }


        return self::$controllers[$name];
    }

    public function notification($inscricao_uuid, Request $request){
        return $this->factory("notification")->notification($inscricao_uuid, $request);
    }

}
