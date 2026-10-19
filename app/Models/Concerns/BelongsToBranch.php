<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToBranch
{
    protected static function bootBelongsToBranch(): void
    {
        // Otomatis isi branch_id saat record dibuat
        static::creating(function ($model) {
            if (empty($model->branch_id)) {
                $model->branch_id = session('active_branch_id', 1);
            }
        });

        // Global scope: selalu filter berdasarkan cabang aktif
        static::addGlobalScope('branch', function (Builder $builder) {
            $builder->where($builder->getModel()->getTable() . '.branch_id', session('active_branch_id', 1));
        });
    }

    /**
     * Bypass branch scope untuk query lintas cabang (misal laporan Owner).
     */
    public static function allBranches(): Builder
    {
        return static::withoutGlobalScope('branch');
    }
}
