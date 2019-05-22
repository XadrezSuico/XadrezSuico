<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use Notifiable;
    use LogsActivity;
    
    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function perfis(){
        return $this->hasMany("App\PerfilUser","users_id","id");
    }

    public function checkPassword(){
        if(mb_strlen($this->password) == 0){
            $this->password = User::find($this->id)->password;
        }
    }
    
    public function isDeletavel(){
        if($this->id != null){
            if($this->perfis()->count() > 0){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }

    public static function canRegisterWithoutLogin(){
        if(count(User::all()) == 0){
            return true;
        }
        return false;
    }

    public function hasPermission($perfis,$grupo_evento_id = NULL, $evento_id = NULL){
        $perfil = $this
            ->perfis()
            ->where(function($q1) use ($perfis){
                $q1->whereIn("perfils_id",$perfis)
                ->where([["grupo_evento_id","=",NULL],["evento_id","=",NULL]]);
            })
            ->orWhere(function($q1) use ($grupo_evento_id, $perfis){
                $q1->whereIn("perfils_id",$perfis)
                ->where([["grupo_evento_id","=",$grupo_evento_id]]);
            })
            ->orWhere(function($q1) use ($evento_id, $perfis){
                $q1->whereIn("perfils_id",$perfis)
                ->where([["evento_id","=",$evento_id]]);
            })
            ->count();
        if($perfil > 0){
            return true;
        }
        return false;
    }

    public function hasPermissionMain($perfis){
        echo $this->perfis()->count();
        if($this->perfis()->whereIn("perfils_id",$perfis)->count() > 0){
            return true;
        }
        return false;
    }
}
