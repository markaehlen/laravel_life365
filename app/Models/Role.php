<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    const ACTIVE = 1;
    const IN_ACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'is_active'
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
     * The attributes casts.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive($query)
    {
        return $query->whereIsActive(self::ACTIVE);
    }

    /**
     * Scope a query to only include role of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfIsActive($query, $type)
    {
        return $query->whereIsActive($type);
    }

    /**
     * Get the users that associated with the role.
     * @return Users
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }

    /**
     * The modules that belong to the role.
     */
    public function modules()
    {
        return $this->belongsToMany('App\Models\Module', 'module_role_permission', 'role_id', 'module_id')->withPivot('permission_id');
    }

    /**
     * Scope a query to only include given filter users.
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

    /*
     * make dynamic attribute for human readable time
     *
     * @return string
     * */

    public function getHumansTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->created_at)->diffForHumans();
    }
}
