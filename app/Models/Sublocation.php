<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Sublocation extends Model
{
    use SoftDeletes;

    const ACTIVE = 1;
    const DEFAULT = 1;
    const IN_ACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'location_id',
        'status',
        'is_marine', 
        'jan_temp',
        'feb_temp',
        'mar_temp',
        'apr_temp',
        'may_temp',
        'jun_temp',
        'jul_temp',
        'aug_temp',
        'sep_temp',
        'oct_temp',
        'nov_temp',
        'dec_temp',
        'avg_temp',
        'snow_day',
        'mean_rh_perc',
        'build_up',
        'std_dev',
        'max_cs',
        'cost_zerofourtwo',
        'cost_black',
        'cost_stainless',
        'cost_epoxy',
        'membrane',
        'cost_sealer',
    ];

    /**
     * The attributes casts.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

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

    public function scopeIsDefault($query)
    {
        return $query->where('is_default', self::DEFAULT);
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


    //Relationships
    public function location()
    {
        return $this->belongsTo('App\Models\Location')->withTrashed();
    }

    public function exposures()
    {
        return $this->belongsToMany('App\Models\Exposure')->withTrashed();
    }
}
