<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToLandlord
{
    public static function bootBelongsToLandlord(): void
    {
        static::addGlobalScope('landlord', function (Builder $builder) {
            $user = auth()->user();

            if ($user && $user->role === 'landlord') {
                $builder->where(
                    $builder->getModel()->getTable().'.landlord_id',
                    $user->getKey()
                );
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->landlord_id)) {
                $user = auth()->user();

                if ($user && $user->role === 'landlord') {
                    $model->landlord_id = $user->getKey();
                }
            }
        });
    }
}
