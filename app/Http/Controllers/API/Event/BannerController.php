<?php

namespace App\Http\Controllers\API\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Image;

class BannerController extends Controller
{
    public function get($uuid){
        if($uuid){
            if(Evento::where([["uuid","=",$uuid]])->count() == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
            }
            $evento = Evento::where([["uuid","=",$uuid]])->first();

            if($evento->pagina){
                $img = Image::make($your_base64_image);

                // Resize
                $img->resize(320, 240);

                // Base64 encoded stream. Also supports 'jpg', 'png' and more...
                $dataUrl = (string) $img->stream('data-uri');
            }else{
                
            }

        }
    }
}
