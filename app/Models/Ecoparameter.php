<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Ecoparameter extends Model
{
    const ACTIVE = 1;
    const IN_ACTIVE = 0;

    protected $table = 'ecoparameters';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('name', 'section', 'type', 'default_value', 'default_unit');

    /**
     * Scope a query to only include active permissions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive($query)
    {
        return $query->whereStatus(self::ACTIVE);
    }

    /**
     * Scope a query to only include permission of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfIsActive($query, $type)
    {
        return $query->whereStatus($type);
    }

    /**
     * Scope a query to only include given filter faq's.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->when($filters['status'] ?? null, function ($query, $status) {
            if ($status === 'active') {
                $query->ofIsActive(self::ACTIVE);
            } elseif ($status === 'in-active') {
                $query->ofIsActive(self::IN_ACTIVE);
            }
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        })->when($filters['sort'] ?? null, function ($query, $sort) {
            $sorter = explode(",", $sort, 2);
            if (count($sorter) == 2) {
                $query->orderBy($sorter[0], $sorter[1]);
            }
        }, function ($query) {
            $query->orderBy('id', 'DESC');
        });
    }
}
