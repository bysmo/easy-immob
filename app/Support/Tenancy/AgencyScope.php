<?php

namespace App\Support\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AgencyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::hasUser() && Auth::user()->agency_id !== null) {
            $builder->where($model->qualifyColumn('agency_id'), Auth::user()->agency_id);
        }
    }
}
