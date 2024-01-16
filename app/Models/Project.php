<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    protected $table = 'projects';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip_address',
        'mac_address',
        'title',
        'description',
        'details',
        'unique_id',
        'inputs'
    ];
}
