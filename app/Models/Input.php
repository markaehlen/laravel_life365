<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Input extends Model
{
    protected $table = 'inputs';

    protected $fillable = array('ip_address', 'mac_address', 'inputs');
}
