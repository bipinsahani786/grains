<?php

namespace App\Traits;

use App\Models\Core\Scopes\TenantScope;

trait BelongsToTenant
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->company_id && empty($model->company_id)) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }
}
