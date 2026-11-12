<?php

namespace App\Scopes;

use App\Enum\RegistrationStatus;
use App\Inscricao;

trait ExcludesDeletedRegistrations
{
    protected static function bootExcludesDeletedRegistrations()
    {
        static::addGlobalScope('not_deleted_registrations', function ($query) {
            foreach ((new static)->registrationColumns() as $column) {
                $column = $query->getModel()->qualifyColumn($column);
                $query->where(function ($query) use ($column) {
                    $query->whereNull($column)->orWhereNotIn($column,
                        Inscricao::withoutGlobalScope('not_deleted')
                            ->select('inscricao.id')->where('inscricao.status', RegistrationStatus::Deleted)
                    );
                });
            }
        });
    }
}
