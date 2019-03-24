<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

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
        return $this->hasMany("App\UserPerfil","users_id","id");
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
}
