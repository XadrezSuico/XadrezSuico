<?php

namespace App\Http\Controllers\API\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Evento;

use Image;
use PlaceholderImage;

class BannerController extends Controller
{
    public function get($uuid){
        if($uuid){
            if(Evento::where([["uuid","=",$uuid]])->count() == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
            }
            $evento = Evento::where([["uuid","=",$uuid]])->first();

            if($evento->pagina){
                if($evento->pagina->imagem){
                    $img = Image::make($evento->pagina->imagem);
                    $img->resize(1230,300);
                    return $img->response();
                }
            }else{
                return $this->placeholder(1230,300,$evento->name);
            }
        }
        return $this->placeholder(1230,300);
    }

    private function placeholder($width, $height, $text = null){
        $img = Image::canvas($width, $height,"#CACACA");

        $text = ($text) ? $text : $width."x".$height;

        // $img->text($text, 120, 100, function($font) {
        //     $font->size(28);
        //     $font->color('#e1e1e1');
        //     $font->align('center');
        //     $font->valign('bottom');
        //     $font->angle(90);
        // });

        $img->text($text,$width/2,$height/2 - (($height/7)/2),function ($font) use ($height) {
            $font->file(public_path('font/clarissans.ttf'));
            $font->size($height/7);
            $font->color("#000");
            $font->align('center');
            $font->valign('top');
        });
        return $img->response();
    }
}
