<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Software extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'softwares';

    public function isChessCom(){
        if($this->name == "Chess.com (Online)"){
            return true;
        }
        return false;
    }

    public function isSwissManager(){
        if($this->name == "Swiss-Manager"){
            return true;
        }
        return false;
    }

    public function isLichess(){
        if($this->name == "Lichess.org (Online)"){
            return true;
        }
        return false;
    }

    public function isXadrezSuico(){
        if($this->name == "XadrezSuíço"){
            return true;
        }
        return false;
    }


    public static function hasChessCom(){
        return Software::where([["name","=","Chess.com (Online)"]])->count() > 0;
    }
    public static function getChessCom(){
        return Software::where([["name","=","Chess.com (Online)"]])->first();
    }

    public static function hasSwissManager(){
        return Software::where([["name","=","Swiss-Manager"]])->count() > 0;
    }
    public static function getSwissManager(){
        return Software::where([["name","=","Swiss-Manager"]])->first();
    }

    public static function hasLichess(){
        return Software::where([["name","=","Lichess.org (Online)"]])->count() > 0;
    }
    public static function getLichess(){
        return Software::where([["name","=","Lichess.org (Online)"]])->first();
    }

    public static function hasXadrezSuico(){
        return Software::where([["name","=","XadrezSuíço"]])->count() > 0;
    }
    public static function getXadrezSuico(){
        return Software::where([["name","=","XadrezSuíço"]])->first();
    }
}
