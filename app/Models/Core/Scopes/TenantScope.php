<?php

namespace App\Models\Core\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check() && auth()->user()->company_id && auth()->user()->role !== 'super_admin') {
            $builder->where($model->getTable() . '.company_id', auth()->user()->company_id);
        } elseif (auth()->check() && auth()->user()->role === 'super_admin' && session()->has('impersonated_by')) {
            // Allow super admin to bypass unless they are impersonating someone
            // If they are impersonating, we scope to the impersonated user's company
            $builder->where($model->getTable() . '.company_id', auth()->user()->company_id);
        }
    }
}
