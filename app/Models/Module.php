<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
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
     * The roles that belong to the module.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'module_role_permission', 'module_id', 'role_id')->withPivot('permission_id');
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
