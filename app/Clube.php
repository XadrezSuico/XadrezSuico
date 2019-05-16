<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Clube extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];
    
    
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'clube';

    public function cidade() {
        return $this->belongsTo("App\Cidade","cidade_id","id");
    }

    public function enxadristas() {
        return $this->hasMany("App\Enxadrista","clube_id","id");
    }

    public function inscricoes() {
        return $this->hasMany("App\Inscricao","clube_id","id");
    }



    public function isDeletavel(){
        if($this->id != null){
            if(
                $this->enxadristas()->count() > 0 ||
                $this->inscricoes()->count() > 0
            ){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }

    public function getName(){
        return mb_strtoupper($this->name);
    }
}
