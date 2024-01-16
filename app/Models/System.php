<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class System extends Model
{
    use SoftDeletes;

    const ACTIVE = 1;
    const BASE = 1;
    const INDEPENDENCY = 1;
    const DEPENDENCY = 0;
    const IN_ACTIVE = 0;

    protected $table = 'systems';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = array('name', 'status');

    public $timestamps = true;

    /**
     * The attributes casts.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function scopeOrderByName(Builder $query)
    {
        $query->orderBy('name');
    }

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

    public function scopeIsBase($query)
    {
        return $query->where('is_base', self::BASE);
    }

    public function scopeIsIndependent($query)
    {
        return $query->where('is_independent', self::INDEPENDENCY);
    }

    public function scopeIsDependent($query)
    {
        return $query->where('is_independent', self::DEPENDENCY);
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
     * Scope a query to only include given filter pages.
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
        })->when($filters['sort'] ?? null, function ($query, $sort) {
            $sorter = explode(",", $sort, 2);
            if (count($sorter) == 2) {
                $query->orderBy($sorter[0], $sorter[1]);
            }
        }, function ($query) {
            $query->orderBy('id', 'DESC');
        });
    }

    public function units()
    {
        return $this->hasMany('App\Models\Unit', 'system_id');
    }
}
