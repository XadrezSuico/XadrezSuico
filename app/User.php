<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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

    public function perfis()
    {
        return $this->hasMany("App\PerfilUser", "users_id", "id");
    }

    public function checkPassword()
    {
        if (mb_strlen($this->password) == 0) {
            $this->password = User::find($this->id)->password;
        }
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if ($this->perfis()->count() > 0) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public static function canRegisterWithoutLogin()
    {
        if (count(User::all()) == 0) {
            return true;
        }
        return false;
    }

    public function hasPermission($perfis, $grupo_evento_id = null, $evento_id = null)
    {
        $perfil = $this
            ->perfis()
            ->where(function ($q1) use ($perfis) {
                $q1->whereIn("perfils_id", $perfis)
                    ->where([["grupo_evento_id", "=", null], ["evento_id", "=", null]]);
            })
            ->orWhere(function ($q1) use ($grupo_evento_id, $perfis) {
                $q1->whereIn("perfils_id", $perfis)
                    ->where([["grupo_evento_id", "=", $grupo_evento_id]]);
            })
            ->orWhere(function ($q1) use ($evento_id, $perfis) {
                $q1->whereIn("perfils_id", $perfis)
                    ->where([["evento_id", "=", $evento_id]]);
            })
            ->count();
        if ($perfil > 0) {
            return true;
        }
        return false;
    }

    public function hasPermissionMain($perfis)
    {
        if ($this->perfis()->whereIn("perfils_id", $perfis)->count() > 0) {
            return true;
        }
        return false;
    }

    /*
     *
     * VERIFICA PERFIL GLOBAL (SUPER-ADMINISTRADOR E ADMINISTRADOR)
     *
     */
    public function hasPermissionGlobal()
    {
        if ($this->perfis()->whereNull("evento_id")->whereNull("grupo_evento_id")->whereIn("perfils_id", [1, 2])->count() > 0) {
            return true;
        }
        return false;
    }
    public function hasPermissionGlobalbyPerfil($perfils)
    {
        if ($this->perfis()->whereNull("evento_id")->whereNull("grupo_evento_id")->whereIn("perfils_id", $perfils)->count() > 0) {
            return true;
        }
        return false;
    }

    /*
     *
     * VERIFICA PERFIL DE EVENTO (DIRETOR DE TORNEIO, ÁRBITRO MESA E ÁRBITRO DE CONFIRMAÇÃO)
     *
     */
    public function hasPermissionEventsByPerfil($perfis)
    {
        if ($this->perfis()->whereNotNull("evento_id")->whereIn("perfils_id", $perfis)->count() > 0) {
            return true;
        }
        return false;
    }
    public function hasPermissionEventByPerfil($evento_id, $perfis)
    {
        if ($this->perfis()->where([["evento_id", "=", $evento_id]])->whereIn("perfils_id", $perfis)->count() > 0) {
            return true;
        }
        return false;
    }
    public function hasPermissionEventByPerfilByGroupEvent($grupo_evento_id, $perfis)
    {
        if ($this->perfis()->whereHas("evento", function ($q1) use ($grupo_evento_id) {
            $q1->where([
                ["grupo_evento_id", "=", $grupo_evento_id],
            ]);
        })->whereIn("perfils_id", $perfis)->count() > 0) {
            return true;
        }
        return false;
    }
    public function getPerfilbyEvent($evento_id)
    {
        $perfil = $this->perfis()->where([["evento_id", "=", $evento_id]])->first();
        if ($perfil) {
            return $perfil;
        }
        return false;
    }

    /*
     *
     * VERIFICA PERFIL DE GRUPO DE EVENTO (DIRETOR DE GRUPO DE EVENTO)
     *
     */
    public function hasPermissionGroupEventsByPerfil($perfis)
    {
        if ($this->perfis()->whereNotNull("grupo_evento_id")->whereIn("perfils_id", $perfis)->count() > 0) {
            return true;
        }
        return false;
    }
    public function hasPermissionGroupEventByPerfil($grupo_evento_id, $perfis)
    {
        if ($this->perfis()->where([["grupo_evento_id", "=", $grupo_evento_id]])->whereIn("perfils_id", $perfis)->count() > 0) {
            return true;
        }
        return false;
    }
    public function getPerfilbyGroupEvent($grupo_evento_id)
    {
        $perfil = $this->perfis()->where([["grupo_evento_id", "=", $grupo_evento_id]])->first();
        if ($perfil) {
            return $perfil;
        }
        return false;
    }
}
