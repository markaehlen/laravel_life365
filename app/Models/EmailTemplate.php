<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class EmailTemplate extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'subject',
        'content',
        'slug',
        'model',
        'class'
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
     * Scope a query to only include ordered named users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderBySubject(Builder $query)
    {
        $query->orderBy('name');
    }

    /**
     * Scope a query to only include given filter email templates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
            $query->orWhere('subject', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        })->when($filters['status'] ?? null, function ($query, $status) {
            if ($status === 'read') {
                $query->ofIsRead(self::READ);
            } elseif ($status === 'unread') {
                $query->ofIsRead(self::UN_READ);
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
