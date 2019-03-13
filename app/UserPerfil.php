<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPerfil extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'users_id', 'perfils_id'
    ];
}
