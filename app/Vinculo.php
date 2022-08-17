<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

class Vinculo extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];


    public static function boot()
    {
        parent::boot();
        self::creating(function($model){
            if($model->uuid == NULL){
                $model->uuid = Str::uuid();
            }
        });
    }
    public function enxadrista(){
        return $this->belongsTo("App\Enxadrista","enxadrista_id","id");
    }
    public function cidade(){
        return $this->belongsTo("App\Cidade","cidade_id","id");
    }
    public function clube(){
        return $this->belongsTo("App\Clube","clube_id","id");
    }


    public function getVinculoType(){
        if($this->is_confirmed_system){
            return "Automático";
        }elseif($this->is_confirmed_manually){
            return "Manual";
        }else{
            return "SEM VÍNCULO";
        }
    }
}
